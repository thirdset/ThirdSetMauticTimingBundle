<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2018 Third Set Productions.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Executioner\Scheduler;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\CampaignBundle\Entity\Event;
use Mautic\CampaignBundle\Executioner\Logger\EventLogger;
use Mautic\CampaignBundle\Executioner\Scheduler\Mode\Interval;
use Mautic\CampaignBundle\Executioner\Scheduler\Mode\DateTime;
use Mautic\CampaignBundle\EventCollector\EventCollector;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

use MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper;

/**
 * Older versions of Mautic don't have the EventScheduler class so we extend
 * a middleman class to maintain backwards compatibility.
 */
if (class_exists('\Mautic\CampaignBundle\Executioner\Scheduler\EventScheduler')) {
    class VersionSafeEventScheduler extends \Mautic\CampaignBundle\Executioner\Scheduler\EventScheduler { }
} else {
    /**
     * Create a class to extend (we won't use it but this will prevent a
     * ClassNotFoundException in older versions of Mautic).
     */
    class VersionSafeEventScheduler {}
}

/**
 * The EventScheduler class extends Mautic's EventScheduler class to add our
 * additional timing and scheduling logic.
 *
 * We extend via a middle man class called VersionSafeEventScheduler (defined
 * above) to prevent ClassNotFoundExceptions on older versions of Mautic.
 *
 * This class works with Mautic v2.14 and up.
 *
 * @package ThirdSetMauticTimingBundle
 * @since 1.2
 */
class EventScheduler extends VersionSafeEventScheduler
{
    /**
     * We store the current contact here so that we can use their time zone
     * when making scheduling decisions. Normally we would pass this in as a
     * method parameter but storing it as a class property allows us to override
     * less of the core Mautic code.
     * @var \Mautic\LeadBundle\Entity\Lead
     */
    protected $currentContact;

    /** @var \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
    protected $timingHelper;

    /**
     * Custom constructor. Extends the standard EventScheduler constructor to
     * also inject the TimingHelper service.
     *
     * @param LoggerInterface          $logger
     * @param EventLogger              $eventLogger
     * @param Interval                 $intervalScheduler
     * @param DateTime                 $dateTimeScheduler
     * @param EventCollector           $collector
     * @param EventDispatcherInterface $dispatcher
     * @param TimingHelper             $timingHelper
     */
    public function __construct(
        LoggerInterface $logger,
        EventLogger $eventLogger,
        Interval $intervalScheduler,
        DateTime $dateTimeScheduler,
        EventCollector $collector,
        EventDispatcherInterface $dispatcher,
        CoreParametersHelper $coreParametersHelper,
        TimingHelper $timingHelper
    ) {
        // Call the parent constructor.
        parent::__construct(
                    $logger,
                    $eventLogger,
                    $intervalScheduler,
                    $dateTimeScheduler,
                    $collector,
                    $dispatcher,
                    $coreParametersHelper
                );

        $this->timingHelper = $timingHelper;
    }

    /**
     * Overrides the standard getExecutionDateTime method to use our custom
     * contact specific timing logic.
     *
     * @param Event          $event The Campaign Event that is being processed.
     * @param \DateTime|null $compareFromDateTime The date to compare from (this
     * would typically be 'now').
     * @param \DateTime|null $comparedToDateTime The date/time to compare to.
     *
     * @return \DateTime
     *
     * @throws NotSchedulableException
     */
    public function getExecutionDateTime(
                        Event $event,
                        \DateTime $compareFromDateTime = null,
                        \DateTime $comparedToDateTime = null
                    )
    {
        // Get the executionDateTime from the parent method.
        $executionDateTime = parent::getExecutionDateTime(
                                        $event,
                                        $compareFromDateTime,
                                        $comparedToDateTime
                                    );

        // Now apply our extended timing rules. Note that contact may be null
        // for certain executioners and that's okay. The only one that we are
        // concerned with is the EventExecutioner and that one is being
        // overridden to set the contact.
        if (null !== $this->currentContact) {
            $executionDateTime = $this->timingHelper->getExecutionDateTime(
                                    $event,
                                    $this->currentContact,
                                    $executionDateTime
                            );
        }

        return $executionDateTime;
    }

    /**
     * Set the current contact (the contact that is currently being scheduled).
     *
     * @param \Mautic\LeadBundle\Entity\Lead $currentContact
     *
     * @return EventScheduler
     */
    public function setCurrentContact(Lead $currentContact)
    {
        $this->currentContact = $currentContact;

        return $this;
    }

    /**
     * Get current contact (the contact that is currently being scheduled).
     *
     * @return \Mautic\LeadBundle\Entity\Lead
     */
    public function getCurrentContact()
    {
        return $this->currentContact;
    }

}

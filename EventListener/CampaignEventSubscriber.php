<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;

use MauticPlugin\ThirdSetMauticTimingBundle\TimingEvents;
use MauticPlugin\ThirdSetMauticTimingBundle\Event\CampaignPreExecutionEvent;
use MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper;

/**
 * Class CampaignEventSubscriber.
 *
 * @package ThirdSetMauticTimingBundle
 */
class CampaignEventSubscriber extends CommonSubscriber
{

    /** @var \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
    private $timingHelper;

    /**
     * Constructor.
     * @param TimingHelper $timingHelper
     */
    public function __construct(
                TimingHelper $timingHelper
            )
    {
        $this->timingHelper = $timingHelper;
    }

    /**
     * Get the list of events that this subscriber subscribes to.
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            TimingEvents::PRE_EVENT_EXECUTION => ['onPreEventExecution', 0],
        ];
    }

    /**
     * Method to be applied to the
     * plugin.thirdset.timing.campaign_pre_event_execution event.
     * @param CampaignPreExecutionEvent $event
     */
    public function onPreEventExecution(CampaignPreExecutionEvent $event)
    {
        // if the eventTriggerDate is already set, just leave it alone.
        if (null === $event->getEventTriggerDate()) {

            $eventTriggerDate = $this->timingHelper->checkEventTiming(
                                            $event->getEventData(),
                                            $event->getParentTriggeredDate(),
                                            $event->allowNegative(),
                                            $event->getLead()
                                        );

            $event->setEventTriggerDate($eventTriggerDate);
        }
    }
}
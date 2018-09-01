<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2018 Third Set Productions.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Executioner;

use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Mautic\CampaignBundle\Executioner\Result\Counter;
use Mautic\CampaignBundle\Entity\LeadRepository;
use Mautic\CampaignBundle\EventCollector\EventCollector;
use Mautic\CampaignBundle\Executioner\Event\ActionExecutioner;
use Mautic\CampaignBundle\Executioner\Event\ConditionExecutioner;
use Mautic\CampaignBundle\Executioner\Event\DecisionExecutioner;
use Mautic\CampaignBundle\Executioner\Logger\EventLogger;
use Mautic\CampaignBundle\Executioner\Scheduler\EventScheduler;
use Mautic\CampaignBundle\Helper\RemovedContactTracker;

/**
 * Older versions of Mautic don't have the EventExecutioner class so we extend
 * a middleman class to maintain backwards compatibility.
 */
if (class_exists('\Mautic\CampaignBundle\Executioner\EventExecutioner')) {
    class VersionSafeEventExecutioner extends \Mautic\CampaignBundle\Executioner\EventExecutioner {
        
       /**
        * Increase the visibility of the scheduler from private to protected so
        * that we can access it.
        * @var EventScheduler
        */
       protected $scheduler;
        
    }
} else {
    /**
     * Create a class to extend (we won't use it but this will prevent a
     * ClassNotFoundException in older versions of Mautic).
     */
    class VersionSafeEventExecutioner {}
}

/**
 * The EventExecutioner class extends Mautic's EventExecutioner class to add our
 * additional timing and scheduling logic. 
 * 
 * We extend via a middle man class called VersionSafeEventExecutioner (defined
 * above) to prevent ClassNotFoundExceptions on older versions of Mautic.
 * 
 * This class was easier to modify than the other executioners because it calls
 * the scheduler from a public method.
 * 
 * This class works with Mautic v2.14 and up.
 * 
 * @package ThirdSetMauticTimingBundle
 * @since 1.2
 */
class EventExecutioner extends VersionSafeEventExecutioner
{   
    
    /**
     * Increase the visibility of the scheduler from private to protected so
     * that we can access it.
     * @var EventScheduler
     */
    protected $scheduler;
    
    /**
     * Constructor
     *
     * @param EventCollector       $eventCollector
     * @param EventLogger          $eventLogger
     * @param ActionExecutioner    $actionExecutioner
     * @param ConditionExecutioner $conditionExecutioner
     * @param DecisionExecutioner  $decisionExecutioner
     * @param LoggerInterface      $logger
     * @param EventScheduler       $scheduler
     * @param LeadRepository       $leadRepository
     */
    public function __construct(
        EventCollector $eventCollector,
        EventLogger $eventLogger,
        ActionExecutioner $actionExecutioner,
        ConditionExecutioner $conditionExecutioner,
        DecisionExecutioner $decisionExecutioner,
        LoggerInterface $logger,
        EventScheduler $scheduler,
        RemovedContactTracker $removedContactTracker,
        LeadRepository $leadRepository
    ) {
        // Call the parent constructor.
        parent::__construct(
            $eventCollector,
            $eventLogger,
            $actionExecutioner,
            $conditionExecutioner,
            $decisionExecutioner,
            $logger,
            $scheduler,
            $removedContactTracker,
            $leadRepository);

        // Make sure that we can access the scheduler
        $this->scheduler = $scheduler;
    }
    
    /**
     * The parent class executes events for a group of contacts. Here we rewrite
     * the method to loop contacts individually so that we can make scheduling
     * decisions on a contact by contact basis.
     * @param ArrayCollection $events
     * @param ArrayCollection $contacts
     * @param Counter|null    $childrenCounter
     * @param bool            $isInactive
     *
     * @throws Dispatcher\Exception\LogNotProcessedException
     * @throws Dispatcher\Exception\LogPassedAndFailedException
     * @throws Exception\CannotProcessEventException
     * @throws Scheduler\Exception\NotSchedulableException
     */
    public function executeEventsForContacts(ArrayCollection $events, ArrayCollection $contacts, Counter $childrenCounter = null, $isInactive = false)
    {
        // Quit if ther are no contacts.
        if (!$contacts->count()) {
            return;
        }
        
        // Loop contacts and process them one at a time.
        foreach ($contacts as $contact) {
            
            // Set the current contact on our custom scheduler.
            $this->scheduler->setCurrentContact($contact);
            
            // Call the parent method with just our current contact.
            parent::executeEventsForContacts(
                        $events, 
                        new ArrayCollection(array($contact)),
                        $childrenCounter,
                        $isInactive
                    );
        }
    }
    
}

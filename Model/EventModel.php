<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Model;

use MauticPlugin\ThirdSetMauticTimingBundle\Event\CampaignPreExecutionEvent;
use MauticPlugin\ThirdSetMauticTimingBundle\TimingEvents;

/**
 * The EventModel class extends Mautic's EventModel class to add our additional
 * events, etc.
 * 
 * @package ThirdSetMauticTimingBundle
 * @since 1.0
 */
class EventModel extends \Mautic\CampaignBundle\Model\EventModel
{   
    /**
     * {@inheritdoc}
     */
    public function executeEvent(
        $event,
        $campaign,
        $lead,
        $eventSettings = null,
        $allowNegative = false,
        \DateTime $parentTriggeredDate = null,
        $eventTriggerDate = null,
        $logExists = false,
        &$evaluatedEventCount = 0,
        &$executedEventCount = 0,
        &$totalEventCount = 0
    ) {
        //dispatch our custom event
        $args = [
                    'event' => $event,
                    'lead'  => $lead,
                ];
        $preExecutionEvent = new CampaignPreExecutionEvent($args);
        $this->dispatcher->dispatch(TimingEvents::PRE_EVENT_EXECUTION, $preExecutionEvent);
        unset($args);
        
        //abort if directed by the event listeners
        if($preExecutionEvent->isExecutionAborted()) {
            $this->logger->debug(
                'CAMPAIGN: Execution aborted for '.ucfirst($event['eventType']).' ID# '.$event['id'].' for contact ID# '.$lead->getId() . ' due to timing rules.'
            );
            unset($event);
            
            return false;
        } else {
        
            //call the parent method        
            return parent::executeEvent(
                $event,
                $campaign,
                $lead,
                $eventSettings,
                $allowNegative,
                $parentTriggeredDate,
                $eventTriggerDate,
                $logExists,
                $evaluatedEventCount,
                $executedEventCount,
                $totalEventCount
            );
        }
    }
}

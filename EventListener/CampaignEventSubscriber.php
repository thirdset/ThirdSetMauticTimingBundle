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

    /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
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
     * Method to be applied to the kernel.controler event.  
     * @param \MauticPlugin\ThirdSetMauticTimingBundle\Event\CampaignPreExecutionEvent $event
     * Note that this is the event system's event not the campaign event.
     */
    public function onPreEventExecution(CampaignPreExecutionEvent $event)
    {
        /** @var $eventData array */
        $eventData = $event->getEvent();
        
        $eventId = $eventData['id'];
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $event->getLead();
        
        //if the event isn't due (according to its timing restrictions), abort execution
        if( ! $this->timingHelper->isDue($eventId, $lead)) {
            $event->abortExection(true);
        }
    }
}
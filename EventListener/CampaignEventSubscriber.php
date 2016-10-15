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
use MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager;

use MauticPlugin\ThirdSetMauticTimingBundle\ThirdParty\Cron\CronExpression;

/**
 * Class CampaignEventSubscriber.
 *
 * @package ThirdSetMauticTimingBundle
 */
class CampaignEventSubscriber extends CommonSubscriber
{

    /* @var $campaignEventManager \MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager */
    private $campaignEventManager;
    
    /**
     * Constructor.
     * @param CampaignEventManager $campaignEventManager
     */
    public function __construct(
                CampaignEventManager $campaignEventManager
            )
    {
        $this->campaignEventManager = $campaignEventManager;
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
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Form\Model\Timing */
        $timing = $this->campaignEventManager->getEventTiming($eventId);
        
        $cron = CronExpression::factory($timing->getExpression());
        
        //if the event isn't due (according to it's cron timing restrictions), abort execution
        if( ! $cron->isDue()) {
            $event->abortExection(true);
        }
    }
}
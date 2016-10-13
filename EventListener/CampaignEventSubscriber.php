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

/**
 * Class CampaignEventSubscriber.
 *
 * @package ThirdSetMauticTimingBundle
 */
class CampaignEventSubscriber extends CommonSubscriber
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        //
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
     */
    public function onPreEventExecution(CampaignPreExecutionEvent $event)
    {
        //$event->abortExection(true);
    }
}
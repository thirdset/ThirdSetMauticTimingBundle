<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Helper;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\CampaignBundle\Model\EventModel;

use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;

use MauticPlugin\ThirdSetMauticTimingBundle\ThirdParty\Cron\CronExpression;

/**
 * Class TimingHelper.
 *
 * @package ThirdSetMauticTimingBundle
 */
class TimingHelper
{

    /* @var $eventModel \Mautic\CampaignBundle\Model\EventModel */
    private $eventModel;
    
    /* @var $timingModel \MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel */
    private $timingModel;
    
    /**
     * Constructor.
     * @param EventModel $eventModel
     * @param TimingModel $timingModel
     */
    public function __construct(
                EventModel $eventModel,
                TimingModel $timingModel
            )
    {
        $this->eventModel = $eventModel;
        $this->timingModel = $timingModel;
    }

    /**
     * Helper method that reviews the timing settings for the passed event and
     * determines if the event is due to be executed for the given Lead.
     * @param integer $eventId The id of the Campaign Event to use for the
     * evaluation.
     * @param Mautic\LeadBundle\Entity\Lead The Lead to use for the evaluation.
     * @param string $initNowStr A string for calulating 'now'.  This is used
     * for testing and can usually be left off.
     * @return boolean Returns true if the event is due, otherwise, returns
     * false.
     */
    public function isDue($eventId, Lead $lead, $initNowStr = 'now')
    {   
        /* @var $timing \Mautic\CampaignBundle\Entity\Event */
        $event = $this->eventModel->getEntity($eventId);
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->timingModel->getEntity($event);
        
        //if there is no timing data for the event, just return true (isDue)
        if($timing == null) {
            return true;
        }
        
        //if the expression is empty/null, just return true (isDue)
        if(empty($timing->getExpression())) {
            return true;
        }
        
        $cron = CronExpression::factory($timing->getExpression());
        
        $timezone = null;
        
        //attempt to use the contact's timezone (if directed)
        if($timing->getUseContactTimezone()) {
            if ($lead->getIpAddresses()) {
                /** @var $ipDetails array */
                $ipDetails = $lead->getIpAddresses()->first()->getIpDetails();
                if( ! empty($ipDetails['timezone'])) {
                    $timezone = $ipDetails['timezone'];
                }
            }
        }
        
        //if no timezone is set yet, try to get it from the Timing settings.
        if(($timezone == null) && ($timing->getTimezone() != null)) {
            $timezone = $timing->getTimezone();
        }
        
        //if no timezone is set yet, use the system's default timezone.
        if($timezone == null) {
            $timezone = date_default_timezone_get();
        }
        
        //calculate now (offset by the timezone)
        $now = new \DateTime($initNowStr);
        $now->setTimezone(new \DateTimeZone($timezone));
        
        //convert $now to a string (otherwise the Cron class will convert the date back to the default timezone)
        $nowStr = $now->format('Y-m-d H:i:s');
        
        //if the event isn't due (according to it's cron timing restrictions), abort execution
        return $cron->isDue($nowStr);
    }
}
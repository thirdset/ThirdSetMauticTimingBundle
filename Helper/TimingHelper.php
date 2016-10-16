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

use MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager;

use MauticPlugin\ThirdSetMauticTimingBundle\ThirdParty\Cron\CronExpression;

/**
 * Class TimingHelper.
 *
 * @package ThirdSetMauticTimingBundle
 */
class TimingHelper
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
     * Helper method that reviews the timing settings for the passed event and
     * determines if the event is due to be executed for the given Lead.
     * @param integer $eventId The id of the Campaign Event to use for the
     * evaluation.
     * @param Mautic\LeadBundle\Entity\Lead The Lead to use for the evaluation.
     * @return boolean Returns true if the event is due, otherwise, returns
     * false.
     */
    public function isDue($eventId, Lead $lead)
    {   
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Form\Model\Timing */
        $timing = $this->campaignEventManager->getEventTiming($eventId);
        
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
        
        echo $timezone . "\n";
        
        //calculate now (offset by the timezone)
        $now = new \DateTime('now', new \DateTimeZone($timezone) );
        
        //convert $now to a string (otherwise the Cron class will convert the date back to the default timezone)
        $nowStr = $now->format('Y-m-d H:i:s');
        
        //if the event isn't due (according to it's cron timing restrictions), abort execution
        return $cron->isDue($nowStr);
    }
}
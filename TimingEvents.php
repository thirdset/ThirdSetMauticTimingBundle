<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle;

/**
 * Class TimingEvents.
 * Events available for ThirdSetMauticTimingBundle.
 */
final class TimingEvents
{

    /**
     * The plugin.thirdset.timing.campaign_pre_event_execution event is
     * dispatched before a campaign event is executed.
     *
     * The event listener receives a
     * MauticPlugin\ThirdSetMauticTimingBundle\Event\CampaignPreExceutionEvent
     * instance.
     *
     * @var string
     */
    const PRE_EVENT_EXECUTION = 'plugin.thirdset.timing.campaign_pre_event_execution';

}

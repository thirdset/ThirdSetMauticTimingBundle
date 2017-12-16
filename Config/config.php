<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2017 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Configure the plugin.
 * Note: there are additional services defined in the ThirdSetMauticTimingBundle
 * class.
 */
return array(
    'name'        => 'Timing',
    'description' => 'Allows for processing actions only during certain times.',
    'version'     => '1.1.6',
    'author'      => 'Third Set Productions',
    'services'    => array(
        //MODELS
        'models' => array(
            'plugin.thirdset.timing.event_timing_model' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel',
            ),
        ),
        //OTHER
        'other' => array(
            //SUBSCRIBERS
            'plugin.thirdset.timing.doctrine_subscriber' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\EventListener\DoctrineSubscriber',
                'tag'       => 'doctrine.event_subscriber',
                'arguments' => [
                    'session',
                ],
            ),
        ),
        //HELPERS
        'helpers' => array(
            'plugin.thirdset.timing.timing_helper' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper',
                'arguments' => [
                    'plugin.thirdset.timing.event_timing_model',
                ]
            ),
        ),
        //EVENT SUBSCRIBERS/LISTENERS (Note: there are more in the "other" section)
        'events' => array(
            'plugin.thirdset.timing.campaign_event_subscriber' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\EventListener\CampaignEventSubscriber',
                'arguments' => 'plugin.thirdset.timing.timing_helper',
            ),
        ),
    ),
     
);
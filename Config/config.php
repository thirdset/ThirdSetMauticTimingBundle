<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
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
    'version'     => '1.0',
    'author'      => 'Third Set Productions',
    'services'    => array(
        //EVENT SUBSCRIBERS/LISTENERS (Note: there are more in the "other" section)
        'events' => array(
            'plugin.thirdset.timing.campaign_pre_execution_event_listener' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\EventListener\CampaignEventSubscriber',
            ),
        ),
        //OTHER
        'other' => array(
            //MANAGERS
            'plugin.thirdset.timing.campaign_event_manager' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager',
                'arguments' => 'doctrine.orm.entity_manager'
            ),
            //SUBSCRIBERS
            'plugin.thirdset.timing.doctrine_subscriber' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticTimingBundle\EventListener\DoctrineSubscriber',
                'tag'       => 'doctrine.event_subscriber',
                'arguments' => [
                    'session',
                ],
            ),
        ),
    ),
     
);
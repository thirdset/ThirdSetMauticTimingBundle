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
        //Note: there are additional services defined in the ThirdSetMauticTimingBundle class.
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
    ),
     
);
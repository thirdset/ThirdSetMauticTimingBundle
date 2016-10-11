<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ThirdSetMauticTimingBundle
 *
 * @package MauticPlugin\ThirdSetMauticTimingBundle
 */
class ThirdSetMauticTimingBundle extends PluginBundleBase
{
    
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        /**
         * Type Extensions
         * Note: these are registered here because Mautic's config system
         * doesn't seem to be able to handle complex tags.
         */
        $container
            ->register(
                'plugin.thirdset.timing.event_type_extension',
                'MauticPlugin\ThirdSetMauticTimingBundle\Form\Extension\EventTypeExtension'
            )
            ->addTag('form.type_extension', array('extended_type' => 'Mautic\CampaignBundle\Form\Type\EventType'));
    }
}
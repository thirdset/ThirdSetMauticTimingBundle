<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class OverrideServiceCompilerPass.
 * 
 * This class has overrides for the Symfony services.
 * See: 
 *  * http://symfony.com/doc/2.8/bundles/override.html#services-configuration
 *  * http://symfony.com/doc/2.8/service_container/compiler_passes.html
 *
 * @package ThirdSetMauticTimingBundle
 */
class OverrideServiceCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        //override the mautic.campaign.model.event service
        $definition = $container->getDefinition('mautic.campaign.model.event');
        $definition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Model\EventModel');
    }
}

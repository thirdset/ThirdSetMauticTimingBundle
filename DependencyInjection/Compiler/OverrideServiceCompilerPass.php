<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2018 Third Set Productions.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
     * Process the compiler pass.
     * @param $container
     */
    public function process(ContainerBuilder $container)
    {
        $schedulerDefinition = null;

        try {
            $schedulerDefinition = $container->getDefinition('mautic.campaign.scheduler');
        } catch (\Exception $ex) {
            //
        }
        if (null !== $schedulerDefinition) {
            // Mautic >= v2.14.0.
            $schedulerDefinition
                    ->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Executioner\Scheduler\EventScheduler')
                    ->addArgument(new Reference('plugin.thirdset.timing.timing_helper'));

            $eventExecutionerDefinition = $container->getDefinition('mautic.campaign.event_executioner');
            $eventExecutionerDefinition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Executioner\EventExecutioner');

            $kickoffExecutionerDefinition = $container->getDefinition('mautic.campaign.executioner.kickoff');
            $kickoffExecutionerDefinition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Executioner\KickoffExecutioner');

            $realtimeExecutionerDefinition = $container->getDefinition('mautic.campaign.executioner.realtime');
            $realtimeExecutionerDefinition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Executioner\RealTimeExecutioner');

            $scheduledExecutionerDefinition = $container->getDefinition('mautic.campaign.executioner.scheduled');
            $scheduledExecutionerDefinition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Executioner\ScheduledExecutioner');
        } else {
            // Mautic < v2.14.0.
            $definition = $container->getDefinition('mautic.campaign.model.event');
            $definition->setClass('MauticPlugin\ThirdSetMauticTimingBundle\Model\EventModel');
        }
    }
}

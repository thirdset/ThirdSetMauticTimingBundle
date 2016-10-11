<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EventTypeExtension
 *
 * @package ThirdSetMauticTimingBundle
 */
class EventTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'Mautic\CampaignBundle\Form\Type\EventType';
    }

    /**
     * Build the form (starting with the form built by the parent type).
     * @param FormBuilderInterface $builder The builder from the parent type.
     * @param array $options Any options from the parent type.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timing', 'text', array(
                    'attr' => array(
                            'title' => 'Enter when the email can be sent.',
                            'style' => 'margin-top: 1em',
                        )
                    )
                );
    }
    
}

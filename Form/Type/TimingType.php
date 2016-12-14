<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;

/**
 * Class EventTypeExtension
 *
 * @package ThirdSetMauticTimingBundle
 */
class TimingType extends AbstractType
{
    /* @var $session \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /* @var $timingModel \MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel */
    private $timingModel;

    /**
     * Constructor.
     * @param Session $session
     * @param TimingModel $timingModel
     */
    public function __construct(
                        Session $session,
                        TimingModel $timingModel
                    )
    {
        $this->session = $session;
        $this->timingModel = $timingModel;
    }

    /**
     * Build the form.
     * @param FormBuilderInterface $builder The builder.
     * @param array $options Any options.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            
        //add the timing_expression field
        $builder->add('expression', 'text', array(
                    'attr' => array(
                            'title' => 'Enter when the email can be sent.',
                            'tooltip'  => 'When is the event allowed to occur? Uses standard crontab notation (Google it!).',
                        )
                    )
                );

        //add the "Use Contact's timezone?" field
        $builder->add('use_contact_timezone', 'yesno_button_group', [
            'label' => 'Use Contact\'s Time Zone?',
            'attr'  => [
                'tooltip' => 'If enabled, the timing expression will be evaluated using the contact\'s time zone. If their time zone isn\'t known, it will fallback to the time zone selected below.',
            ],
        ]);

        //add the "Add the timezone" field
        $builder->add('timezone', 'Symfony\Component\Form\Extension\Core\Type\TimezoneType', array(
                'label'      => 'Time Zone',
                'attr'       => array(
                    'class'   => 'form-control',
                    'tooltip' => 'Choose the time zone to use when evaluating the timing expression.',
                ),
                'multiple'    => false,
                'placeholder' => 'mautic.user.user.form.defaulttimezone',
                'required'    => false,
            )
        );
    }
    
    /**
     * Returns the name associated with the form.
     * @return string
     */
    public function getName()
    {
        return 'timing';
    }
}

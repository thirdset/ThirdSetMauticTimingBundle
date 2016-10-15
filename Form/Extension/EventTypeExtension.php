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
use Symfony\Component\HttpFoundation\Session\Session;

use MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager;
use MauticPlugin\ThirdSetMauticTimingBundle\EventListener\TimingFormSubscriber;

/**
 * Class EventTypeExtension.
 * 
 * This extension is registered in the ThirdSetMauticTimingBundle class.
 *
 * @package ThirdSetMauticTimingBundle
 */
class EventTypeExtension extends AbstractTypeExtension
{
    /* @var $session \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /* @var $campaignEventManager \MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager */
    private $campaignEventManager;

    /**
     * Constructor.
     * @param Session $session
     * @param CampaignEventManager $campaignEventManager
     */
    public function __construct(
                        Session $session,
                        CampaignEventManager $campaignEventManager
                    )
    {
        $this->session = $session;
        $this->campaignEventManager = $campaignEventManager;
    }
    
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
        //if this is an 'action' or 'condition' event, add the timing form.
        if (in_array($options['data']['eventType'], ['action', 'condition'])) {
            
            //add timing form
            $builder->add('timing', 'timing', array(
                    'required'    => false,
                )
            );
            
            //add timing form event subscriber
            $builder->addEventSubscriber(
                        new TimingFormSubscriber(
                                $this->session,
                                $this->campaignEventManager
                        )
                    );
            
        }
    }
    
}

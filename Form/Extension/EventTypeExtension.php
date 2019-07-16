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

use Mautic\CampaignBundle\Model\EventModel;

use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;
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
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /** @var \Mautic\CampaignBundle\Model\EventModel */
    private $eventModel;

    /** @var \MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel */
    private $timingModel;

    /**
     * Constructor.
     * @param Session $session
     * @param EventModel $eventModel
     * @param TimingModel $timingModel
     */
    public function __construct(
                        Session $session,
                        EventModel $eventModel,
                        TimingModel $timingModel
                    )
    {
        $this->session = $session;
        $this->eventModel = $eventModel;
        $this->timingModel = $timingModel;
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
        // If this is an 'action' or 'condition' event, add the timing form.
        if (in_array($options['data']['eventType'], ['action', 'condition'])) {

            // Add timing form.
            $builder->add('timing', 'timing', array(
                    'required'    => false,
                )
            );
            //var_dump($options['data']); //For debugging (goes to AJAX response).

            // Get the event (or use null) if this is a new Campaign.
            $event = null;
            $update = (!empty($options['data']['id']) && strpos($options['data']['id'], 'new') === false) ? true : false;
            if ($update) {
                $eventId = $options['data']['id'];

                /* @var $event \Mautic\CampaignBundle\Entity\Event */
                $event = $this->eventModel->getEntity($eventId);
            }

            // Add timing form event subscriber.
            $builder->addEventSubscriber(
                        new TimingFormSubscriber(
                                $this->session,
                                $event,
                                $this->timingModel
                        )
                    );
        }
    }

}

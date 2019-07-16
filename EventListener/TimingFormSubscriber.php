<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;

use Mautic\CampaignBundle\Entity\Event;

use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;

/**
 * Class TimingFormSubscriber. Subscribes to events that occur with the Timing
 * form.
 *
 * Note: this subscriber is registered in the Form\Extension\EventTypeExtension
 * class.
 *
 * @package ThirdSetMauticTimingBundle
 */
class TimingFormSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /** @var \Mautic\CampaignBundle\Entity\Event */
    private $event;

    /** @var \MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel */
    private $timingModel;

    /**
     * Constructor.
     * @param Session $session
     * @param Event|null $event
     * @param TimingModel $timingModel
     */
    public function __construct(
                        Session $session,
                        Event $event = null,
                        TimingModel $timingModel
                    )
    {
        $this->session = $session;
        $this->event = $event;
        $this->timingModel = $timingModel;
    }

    /**
     * Get the list of events that this subscriber subscribes to.
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    /**
     * Called when a form is pre-populated.
     *
     * Add any timing data to the formEvent.
     *
     * @param FormEvent $formEvent
     */
    public function onPreSetData(FormEvent $formEvent)
    {
        // Get the data from the from event.
        $data = $formEvent->getData();

        // If the timing isn't set, try to get it from the db.
        if ((!isset($data['timing']['expression'])) && (null !== $this->event)) {

            // Retrieve the campaign event timing from the db.
            /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
            $timing = $this->timingModel->getTimingForEvent($this->event);

            // Add the campaign event timing to the data.
            $data['timing']['expression'] = $timing->getExpression();
            $data['timing']['use_contact_timezone'] = $timing->useContactTimezone();
            $data['timing']['timezone'] = $timing->getTimezone();

            // Set our modified data as the data to be sent to the form.
            $formEvent->setData($data);
        }
    }

}

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

use MauticPlugin\ThirdSetMauticTimingBundle\Model\CampaignEventManager;

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
     * Add any timing data to the event.
     * 
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        //get the data from the from event
        $data = $event->getData();
        
        //if the timing isn't set, try to get it from the db.
        if( ! isset($data['timing']['timing_expression'])) {
        
            //pull the campaign event id from the form data
            $eventId = $data['id'];

            //retrieve the campaign event timing from the db.
            /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Form\Model\Timing */
            $timing = $this->campaignEventManager->getEventTiming($eventId);

            //add the campaign event timing to the data.
            $data['timing']['timing_expression'] = $timing->getExpression();
            $data['timing']['timing_use_contact_timezone'] = $timing->getUseContactTimezone();
            $data['timing']['timing_timezone'] = $timing->getTimezone();

            //set our modified data as the data to be sent to the form
            $event->setData($data);
        }
    }
    
}

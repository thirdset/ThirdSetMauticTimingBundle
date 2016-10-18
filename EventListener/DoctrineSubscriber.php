<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

use Symfony\Component\HttpFoundation\Session\Session;

use MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing;

/**
 * Class DoctrineSubscriber.
 *
 * @package ThirdSetMauticTimingBundle
 */
class DoctrineSubscriber implements EventSubscriber
{
    /* @var $session \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /**
     * Constructor.
     * @param Session $session
     */
    public function __construct(
                        Session $session
                    )
    {
        $this->session = $session;
    }

    /**
     * Get the list of events that this subscriber subscribes to.
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * Function for subscribing to postPersist events. Note: these are just
     * for new entities, existing entities throw the postUpdate event.
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->saveTimingData($args);
    }

    /**
     * Function for subscribing to postUpdate events. Note: these are just
     * for existing entities, new entities throw the postPersist event.
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->saveTimingData($args);
    }
    
    /**
     * Private helper method for adding timing data to an Event.
     * 
     * Note: we access the db from within this function (instead of a model
     * class) to avoid circular references.
     * 
     * @param LifecycleEventArgs $args
     */
    private function saveTimingData(LifecycleEventArgs $args)
    {   
        $entity = $args->getEntity();

        if($entity instanceof \Mautic\CampaignBundle\Entity\Event) {
            /** @var \Mautic\CampaignBundle\Entity\Event $event */
            $event = $entity;
            
            $campaignId = $event->getCampaign()->getId();
            
            //get the timing data out of the session
            $modifiedEvents = $this->session->get('mautic.campaign.'.$campaignId.'.events.modified', []);
            $eventData = $modifiedEvents[$event->getId()];
            $timingArr = $eventData['timing'];
            
            //get the timing object (note: we have to go through the attached em to prevent a circular reference)
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $args->getEntityManager();
            /* @var $timingRepository \MauticPlugin\ThirdSetMauticTimingBundle\Entity\TimingRepository */
            $timingRepository = $em->getRepository('ThirdSetMauticTimingBundle:Timing');
            /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
            $timing = $timingRepository->getEntity($event->getId());
            
            //if there isn't any timing data yet, create a new Timing Entity.
            if($timing == null) {
                $timing = new Timing($event);
            }
            
            //add the new data
            $timing->addPostData($timingArr);
            
            //persist the Timing
            $em->persist($timing);
            $em->flush();
        }
    }
}

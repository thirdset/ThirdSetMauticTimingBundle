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
     * Note: we access the db from within this function (instead of a manager,
     * model class) because the EM included in the args is set to avoid circular
     * references.
     * 
     * @param LifecycleEventArgs $args
     */
    private function saveTimingData(LifecycleEventArgs $args)
    {   
        $entity = $args->getEntity();

        if($entity instanceof \Mautic\CampaignBundle\Entity\Event) {
            /** @var \Mautic\CampaignBundle\Entity\Event $event */
            $event = $entity;
            
            $campaignId = $event->getCampaign()->getId();;
            
            //get the timing data out of the session
            $modifiedEvents = $this->session->get('mautic.campaign.'.$campaignId.'.events.modified', []);
            $eventData = $modifiedEvents[$event->getId()];
            $timing = $eventData['timing'];
            
            //update the event with the timing data
            $em = $args->getEntityManager();
            
            /* @var $qb \Doctrine\DBAL\Query\QueryBuilder */
            $qb = $em->getConnection()->createQueryBuilder();

            $qb->update(MAUTIC_TABLE_PREFIX . 'campaign_events')
                ->set('timing', ':timing')
                ->where('id = :id')
                ->setParameter('timing', $timing)
                ->setParameter('id', $event->getId())
                ->execute();
        }
    }
}

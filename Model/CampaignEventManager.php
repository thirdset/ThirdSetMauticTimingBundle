<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace MauticPlugin\ThirdSetMauticTimingBundle\Model;

use Doctrine\ORM\EntityManager;

/**
 * The CampaignEventManager class contains custom methods for managing campaign
 * Events.
 * 
 * @package ThirdSetMauticTimingBundle
 * @since 1.0
 */
class CampaignEventManager
{   
    /* @var $em \Doctrine\ORM\EntityManager */
    private $em;
    
    /**
     * Constructor.
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(
                        EntityManager $em
                    )
    {   
        $this->em = $em;
    }
    
    /**
     * Gets the timing data for the passed Event id.
     *
     * This function is needed because the timing field isn't part of the actual
     * entity.
     *
     * @param int $eventId The id of the Event that you want to get the timing
     * data for.
     * @return string Returns the timing data for the Event
     */
    public function getEventTiming(
                        $eventId
                    )
    {
        /* @var $qb \Doctrine\DBAL\Query\QueryBuilder */
        $qb = $this->em->getConnection()->createQueryBuilder();

        $timing = $qb->select(timing)
                ->from(MAUTIC_TABLE_PREFIX . 'campaign_events')
                ->where('id = :id')
                ->setParameter('id', $eventId)
                ->execute()
                ->fetchColumn();
        
        return $timing;
    }
}

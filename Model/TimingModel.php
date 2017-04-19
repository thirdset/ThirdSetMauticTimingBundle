<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Model;

use Mautic\CoreBundle\Model\FormModel as CommonFormModel;
use Mautic\CampaignBundle\Entity\Event;

use MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing;

/**
 * Class TimingModel
 * {@inheritdoc}
 */
class TimingModel extends CommonFormModel
{

    /**
     * EvetTimingModel constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\ThirdSetMauticTimingBundle\Entity\TimingRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('ThirdSetMauticTimingBundle:Timing');
    }
    
    /**
     * Gets a Timing entity by its id or generates a new Timing entity if no id
     * is passed.
     *
     * @param null|integer $id The id of the Timing entity.
     *
     * @return Entity Returns the Entity specified by $id or generates a new one
     * if it doesn't yet exist.
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new Timing();
        }

        return parent::getEntity($id);
    }

    /**
     * Get a specific Timing entity or generate a new one if it doesn't yet
     * exist.
     *
     * @param $event Event The event that the Timing is for.
     *
     * @return null|Timing Returns the Timing for the passed Event.
     */
    public function getTimingForEvent(Event $event = null)
    {
        if ($event === null) {
            return new Timing($event);
        }

        $entity = parent::getEntity($event->getId());
        
        if($entity == null) {
            $entity = new Timing($event);
        }

        return $entity;
    }
    
    /**
     * Get a specific Timing entity.
     * @param $id The id of the Timing entity that you want to get.
     * @return Timing
     */
    public function getById($id)
    {
        $entity = parent::getEntity($id);

        return $entity;
    }

}

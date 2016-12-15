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
     * Get a specific entity or generate a new one doesn't yet exist.
     *
     * @param $event Event
     *
     * @return null|Timing
     */
    public function getEntity(Event $event = null)
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

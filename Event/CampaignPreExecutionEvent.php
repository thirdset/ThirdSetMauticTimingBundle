<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Mautic\LeadBundle\Entity\Lead;

/**
 * Class CampaignPreExecutionEvent.
 */
class CampaignPreExecutionEvent extends Event
{
    /**
     * @var array
     */
    protected $eventData;

    /**
     * @var \DateTime;
     */
    protected $parentTriggeredDate;

    /**
     *
     * @var boolean
     */
    protected $allowNegative;

    /**
     * @var \Mautic\LeadBundle\Entity\Lead;
     */
    protected $lead;

    /**
     *
     * @var \DateTime|boolean
     */
    protected $eventTriggerDate;


    /**
     * Constructor.
     * @param array $eventData An array of campaign Event data.
     * @param \DateTime|null $parentTriggeredDate
     * @param bool $allowNegative
     * @param \DateTime|bool $eventTriggerDate The current eventTriggerDate
     */
    public function __construct(
                        $eventData,
                        \DateTime $parentTriggeredDate = null,
                        $allowNegative = false,
                        Lead $lead,
                        $eventTriggerDate = null
                    )
    {
        $this->eventData = $eventData;
        $this->parentTriggeredDate = $parentTriggeredDate;
        $this->allowNegative = $allowNegative;
        $this->lead = $lead;
        $this->eventTriggerDate = $eventTriggerDate;
    }

    /**
     * Get the campaign event's data array.
     * @return array An array of data for the campaign event.
     */
    public function getEventData()
    {
        return $this->eventData;
    }

    /**
     * Get the trigger \DateTime of the campaign Event's parent.
     * @return \DateTime The trigger \DateTime of the campaign Event's parent.
     */
    public function getParentTriggeredDate()
    {
        return $this->parentTriggeredDate;
    }

    /**
     * Whether or not to allow negative (for 'no' decision paths).
     * @return boolean Returns whether or not to allow negative.
     */
    public function allowNegative()
    {
        return $this->allowNegative;
    }

    /**
     * Get the Lead associated with the event.
     * @return Mautic\LeadBundle\Entity\Lead The Lead/Contact associated with
     * the event.
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * Get the campaign Event trigger DateTime if in the future, or true if the
     * campaign Event is already due.
     * @return \DateTime|boolean The campaign Event trigger \DateTime if in the
     * future or true if the campaign Event is already due.
     */
    public function getEventTriggerDate()
    {
        return $this->eventTriggerDate;
    }

    /**
     * Sets the campaign EventTriggerDate to allow for filtering the value.
     * @param \DateTime|boolean $eventTriggerDate The campaign Event trigger
     * \DateTime if in the future or true if the campaign Event is already due.
     */
    public function setEventTriggerDate($eventTriggerDate)
    {
        $this->eventTriggerDate = $eventTriggerDate;
    }

}

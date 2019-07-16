<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CampaignBundle\Entity\Event;

/**
 * The Timing class is an Entity class that holds timing data for an event.
 * It holds a one-to-one relationship with the Entity class.
 */
class Timing
{
    /**
     * The event that the Timing is for.
     * @var Event
     */
    private $event;

    /**
     * The cron expression for the Timing.
     * @var string
     */
    private $expression;

    /**
     * Whether or not to use the contact's timezone.
     * @var boolean
     */
    private $useContactTimezone;

    /**
     * The timezone for the Timing (ex: "America/Los_Angeles").
     * @var string
     */
    private $timezone;

    /**
     * Constructor.
     * @param Event $event|null The event that the Timing is for.
     * @param string|null $expression The cron expression for the Timing.
     * @param boolean|null $useContactTimezone Whether or not to use the
     * contact's timezone.
     * @param string|null $timezone The timezone for the Timing
     * (ex: "America/Los_Angeles")
     */
    public function __construct(
                        Event $event = null,
                        $expression = null,
                        $useContactTimezone = null,
                        $timezone = null
                    )
    {
        $this->event = $event;
        $this->expression = $expression;
        $this->useContactTimezone = $useContactTimezone;
        $this->timezone = $timezone;
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('campaign_events_timing')
            ->setCustomRepositoryClass('MauticPlugin\ThirdSetMauticTimingBundle\Entity\TimingRepository');

        $builder->createOneToOne('event', 'Mautic\CampaignBundle\Entity\Event')
            ->isPrimaryKey()
            ->addJoinColumn('event_id', 'id', false, true, 'CASCADE')
            ->build();

        $builder->createField('expression', 'string')
            ->columnName('expression')
            ->nullable()
            ->build();

        $builder->createField('useContactTimezone', 'integer')
            ->columnName('use_contact_timezone')
            ->nullable()
            ->build();

        $builder->createField('timezone', 'string')
            ->columnName('timezone')
            ->nullable()
            ->build();
    }

    /**
     * Method to post data to the Timing entity.
     * @param Event $event The eventId of the Campaign Event that the Timing
     * belongs to.
     * @param array $dataArray The post data to be added to the Timing entity.
     */
    public function addPostData($dataArray)
    {
        $this->expression = $dataArray['expression'];

        $useContactTimezone = ( ! empty($dataArray['use_contact_timezone'])) ? $dataArray['use_contact_timezone'] : 0;
        $this->useContactTimezone = $useContactTimezone;

        $this->timezone = $dataArray['timezone'];
    }

    /**
     * Set event.
     *
     * @param \Mautic\CampaignBundle\Entity\Event $event
     *
     * @return Timing
     */
    public function setEvent(\Mautic\CampaignBundle\Entity\Campaign $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event.
     *
     * @return \Mautic\CampaignBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return array
     */
    public function convertToArray()
    {
        return get_object_vars($this);
    }

    /**
     * Sets the cron expression.
     * @param string The cron expression.
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    }

    /**
     * Gets the cron expression.
     * @return string Returns the cron expression.
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Sets whether or not to use the contact's timezone.
     * @param boolean Whether or not to use the contact's timezone.
     */
    public function setUseContactTimezone($useContactTimezone)
    {
        $this->useContactTimezone = $useContactTimezone;
    }

    /**
     * Gets whether or not to use the contact's timezone.
     * @return boolean Returns whether or not to use the contact's timezone.
     */
    public function useContactTimezone()
    {
        return $this->useContactTimezone;
    }

    /**
     * Sets the timezone.
     * @param string The timezone.
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Gets the timezone.
     * @return string Returns the timezone.
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

}

<?php

/*
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2019 Third Set Productions.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Helper;

use Mautic\CampaignBundle\Entity\Event;
use Mautic\LeadBundle\Entity\Lead;

use MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing;
use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;

use MauticPlugin\ThirdSetMauticTimingBundle\ThirdParty\Cron\CronExpression;

/**
 * Class TimingHelper.
 *
 * @package ThirdSetMauticTimingBundle
 * @since 1.0
 */
class TimingHelper
{
    /** @var \MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel */
    private $timingModel;

    /**
     * Constructor.
     * @param TimingModel $timingModel
     */
    public function __construct(
                TimingModel $timingModel
            )
    {
        $this->timingModel = $timingModel;
    }

    /**
     * Gets the executionDateTime with our extended timing rules applied.
     *
     * This is used by Mautic 2.14 and up.
     *
     * @param \Mautic\CampaignBundle\Entity\Event $event The Campaign Event to
     * use for the evaluation.
     * @param \Mautic\LeadBundle\Entity\Lead $contact The Contact to use for the
     * evaluation.
     * @param \DateTime $executionDateTime The executionDateTime that was
     * determined by Mautic's standard timing rules.
     * @param string $initNowStr A string for calulating 'now'. This can be used
     * to find the nextRunDate based on some future now date.
     * @return \DateTime Returns the executionDateTime.
     */
    public function getExecutionDateTime(
                        Event $event,
                        Lead $contact,
                        \DateTime $executionDateTime,
                        $initNowStr = 'now'
                    )
    {
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->timingModel->getById($event->getId());

        // If there is no timing data for the event, return the received
        // executionDateTime as is.
        if (null === $timing) {
            return $executionDateTime;
        }

        // If the timing expression is empty/null, return the received
        // executionDateTime as is.
        if (empty($timing->getExpression())) {
            return $executionDateTime;
        }

        // Get the nextRunDate (next time that the trigger can be run according
        // to our extended timing rules).
        $nextRunDate = $this->getNextRunDate(
                                $timing,
                                $contact,
                                $executionDateTime->format('Y-m-d H:i:s')
                        );

        return $nextRunDate;
    }

    /**
     * Our custom checkEventTiming function.
     * @param array $eventData An array of event data.
     * @param \DateTime|null $parentTriggeredDate
     * @param boolean $allowNegative
     * @param Mautic\LeadBundle\Entity\Lead The Lead to use for the evaluation.
     * @param string $initNowStr A string for calulating 'now'.  This is used
     * for testing and can usually be left off.
     * @return \DateTime|boolean|null Returns one of:
     *  * The DateTime when the event should be triggered (if in the future)
     *  * true if the event is due now
     *  * false if there is an error/issue.
     *  * null if we want to pass through and let it be determined by core
     *    methods.
     */
    public function checkEventTiming(
                        $eventData,
                        \DateTime $parentTriggeredDate = null,
                        $allowNegative = false,
                        Lead $lead,
                        $initNowStr = 'now'
                    )
    {
        $eventId = $eventData['id'];

        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->timingModel->getById($eventId);

        // If there is no timing data for the event, return null (hand off to
        // core methods).
        if (null === $timing) {
            return null;
        }

        // If the timing expression is empty/null, return null (hand off to core
        //methods).
        if (empty($timing->getExpression())) {
            return null;
        }

        // Get the dueDate (according to the standard Mautic settings).
        $dueDate = $this->getDueDate(
                            $eventData,
                            $parentTriggeredDate,
                            $allowNegative,
                            $initNowStr
                    );

        // If the standard mautic logic returns null, return null (hand off to
        // core methods).
        if (null === $dueDate) {
            return null;
        }

        // Get the nextRunDate (next time that the trigger can be run according
        // to our extended timing rules).
        $nextRunDate = $this->getNextRunDate(
                                $timing,
                                $lead,
                                $dueDate->format('Y-m-d H:i:s')
                        );

        $now = new \DateTime($initNowStr);

        if ($nextRunDate <= $now) {
            return true; // Trigger now.
        } else {
            return $nextRunDate; // Schedule.
        }
    }

    /**
     * Private helper function to calculate the due date based on the standard
     * Mautic timing logic/fields.
     *
     * Most of the logic here is copy/pasta from the
     * Mautic\CampaignBundle\Model\EventModel->checkEventTiming function
     *
     * @param array $action An array of data from an action Event.
     * @param \DateTime|null $parentTriggeredDate
     * @param boolean $allowNegative
     * @param string $initNowStr A string for calulating 'now'.  This is used
     * for testing and can usually be left off.
     * @return DateTime|null
     */
    private function getDueDate(
                            $action,
                            \DateTime $parentTriggeredDate = null,
                            $allowNegative = false,
                            $initNowStr = 'now'
    ) {
        if (('no' === $action['decisionPath']) && (!$allowNegative)) {
            return null;
        }

        // Default the dueDate to now.
        $dueDate = new \DateTime($initNowStr);

        if ($action['triggerMode'] == 'interval') {
            if (('no' === $action['decisionPath']) && ($allowNegative)) {
                $dueDate = clone $parentTriggeredDate;
            }

            $interval = $action['triggerInterval'];
            $unit     = strtoupper($action['triggerIntervalUnit']);

            switch ($unit) {
                case 'Y':
                case 'M':
                case 'D':
                    $dt = "P{$interval}{$unit}";
                    break;
                case 'I':
                    $dt = "PT{$interval}M";
                    break;
                case 'H':
                case 'S':
                    $dt = "PT{$interval}{$unit}";
                    break;
            }

            $dv = new \DateInterval($dt);
            $dueDate->add($dv);

        } elseif ('date' === $action['triggerMode']) {
            $dueDate = $action['triggerDate'];
        }

        return $dueDate;
    }

    /**
     * Private helper function that gets the next run date based on our timing
     * rules.
     * @param Timing $timing The timing rules object to use for the calculation
     * this should have been pre-screened for an expression, etc.
     * @param Mautic\LeadBundle\Entity\Lead The Lead to use for the evaluation.
     * @param string $initNowStr A string for calulating 'now'. This can be used
     * to find the nextRunDate based on some future now date.
     * @return \DateTime|boolean Returns the DateTime to trigger the event (if
     * in the future), true if the event is due now or false if there is an
     * error/issue.
     */
    private function getNextRunDate(
                        Timing $timing,
                        Lead $lead,
                        $initNowStr = 'now'
                    )
    {
        $cron = CronExpression::factory($timing->getExpression());

        $timezone = null;

        // Attempt to use the contact's timezone (if directed).
        if ($timing->useContactTimezone()) {

            if (method_exists($lead, 'getTimezone')) {
                $timezone = $lead->getTimezone();
            }
            if ((empty($timezone)) && (!$lead->getIpAddresses()->isEmpty())) {
                /* @var $ipDetails array */
                $ipDetails = $lead->getIpAddresses()->first()->getIpDetails();
                if (!empty($ipDetails['timezone'])) {
                    $timezone = $ipDetails['timezone'];
                }
            }
        }

        // If no timezone is set yet, try to get it from the Timing settings.
        if ((empty($timezone)) && (null !== $timing->getTimezone())) {
            $timezone = $timing->getTimezone();
        }

        // If no timezone is set yet, use the system's default timezone.
        if (empty($timezone)) {
            $timezone = date_default_timezone_get();
        }

        // Calculate 'now' (offset by timezone).
        $now = new \DateTime($initNowStr);
        $now->setTimezone(new \DateTimeZone($timezone));

        // Calculate the next run date.
        // See https://github.com/mtdowling/cron-expression/blob/master/src/Cron/CronExpression.php
        $nextRunDate = $cron->getNextRunDate(
                                    $now, // Current time.
                                    0, // Number of matches to skip before returning a matching next run date.
                                    true // allowCurrentDate: Set to TRUE to return the current date if it matches the cron expression.
                                );

        return $nextRunDate;
    }
}

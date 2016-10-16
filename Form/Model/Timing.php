<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Form\Model;

/**
 * The Timing class is used to store timing settings for a Campaign Event.
 * 
 * @package ThirdSetMauticTimingBundle
 * @since 1.0
 */
class Timing
{   
    /** @var string */
    private $expression;
    
    /** @var boolean */
    private $useContactTimezone;
    
    /** @var string */
    private $timezone;
    
    /**
     * Constructor.
     * @param type $expression
     * @param type $useContactTimezone
     * @param type $timezone
     */
    public function __construct(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    ) 
    {
        $this->expression = $expression;
        $this->useContactTimezone = $useContactTimezone;
        $this->timezone = $timezone;
    }
    
    /**
     * Static function to create a new Timing object from a data array. Call 
     * using Timing::createFromDataArray($dataArray);
     * @param array $dataArray Db data to be used to construct the Timing
     * object.
     * @return \self Works like a constructor.
     */
    public static function createFromDataArray($dataArray)
    {
        $useContactTimezone = ( ! empty($dataArray['timing_use_contact_timezone'])) ? $dataArray['timing_use_contact_timezone'] : 0;
        
        $instance = new self(
                    $dataArray['timing_expression'],
                    $useContactTimezone,
                    $dataArray['timing_timezone']
                );
        
        return $instance;
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
    public function getUseContactTimezone()
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

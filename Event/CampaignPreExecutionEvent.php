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

/**
 * Class CampaignPreExecutionEvent.
 */
class CampaignPreExecutionEvent extends Event
{ 

    /**
     * @var array
     */
    protected $event;
    
    /**
     *
     * @var boolean 
     */
    protected $abortExecution;


    /**
     * Construct.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->event = $args['event'];
        
        $this->abortExecution = false;
    }

    /**
     * @return array
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    /**
     * Get whenther or not execution should be aborted.
     * @return boolean
     */
    public function isExecutionAborted()
    {
        return $this->abortExecution;
    }
    
    /**
     * Set whether or not execution should be aborted.
     * @param boolean $abortExecution
     */
    public function abortExection($abortExecution)
    {
        $this->abortExecution = $abortExecution;
    }

}

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
    protected $event;
    
    /**
     * @var \Mautic\LeadBundle\Entity\Lead;
     */
    protected $lead;
    
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
        $this->lead = $args['lead'];
        
        $this->abortExecution = false;
    }

    /**
     * Get the campaign event's data array.
     * @return array An array of data for the campaign event. 
     */
    public function getEvent()
    {
        return $this->event;
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

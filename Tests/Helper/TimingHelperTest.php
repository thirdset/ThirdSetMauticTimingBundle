<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Tests\Helper;

use MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing;
use MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper;

/**
 * Class TimingHelper test has tests for the TimingHelper class.
 */
class TimingHelperTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @testdox isDue correctly returns true for a due simple expression.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsTrueForADueSimpleExpression()
    {
        //the time and expression would should return true.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertTrue($isDue);
    }
    
    /**
     * @testdox isDue correctly returns false for a not due simple expression.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsFalseForANotDueSimpleExpression()
    {
        //the time and expression would should return true.
        $mockNow = '2016-01-01 01:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertFalse($isDue);
    }
    
    /**
     * @testdox isDue correctly returns true when a lead's time zone makes it
     * due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsTrueWhenContactsTimezoneMakesItDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 01-06 * * *';
        $useContactTimezone = true;
        $contactTimezone = 'America/New_York'; // -5
        
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertTrue($isDue);
    }
    
    /**
     * @testdox isDue correctly returns false when a lead's time zone makes it
     * not due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsFalseWhenContactsTimezoneMakesItNotDue()
    {
        //the time and expression would return true, but the offset should 
        //cause them to return false instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = true;
        $contactTimezone = 'America/New_York'; // -5
        
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertFalse($isDue);
    }
    
    /**
     * @testdox isDue correctly returns true when the Timing's time zone makes 
     * it due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsTrueWhenTimingTimezoneMakesItDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 01-06 * * *';
        $useContactTimezone = false;
        $timezone = 'America/New_York'; // -5
        $contactTimezone = null;
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertTrue($isDue);
    }
    
    /**
     * @testdox isDue correctly returns false when the Timing's time zone makes 
     * it not due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsFalseWhenTimingTimezoneMakesItNotDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $timezone = 'America/New_York'; // -5
        $contactTimezone = null;
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, $mockNow);
        
        $this->assertFalse($isDue);
    }
    
    /**
     * @testdox isDue correctly returns true when the event doesn't have timing
     * data.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsTrueWhenEventDoesntHaveTimingData()
    {   
        //mock the Event
        $event = $this->getMockBuilder('\Mautic\CampaignBundle\Entity\Event')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //mock the eventModel
        $eventModel = $this->getMockBuilder('\Mautic\CampaignBundle\Model\EventModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the eventModel->getEntity() function
        $eventModel->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($event));

        //mock the timingModel
        $timingModel = $this->getMockBuilder('\MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the timingModel->getEntity() function
        $timingModel->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(null));
                
        //create the timingHelper
        $timingHelper = new TimingHelper(
                            $eventModel,
                            $timingModel
                        );
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead(null);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, 'now');
        
        $this->assertTrue($isDue);
    }
    
    /**
     * @testdox isDue correctly returns true when the event doesn't have timing
     * data.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testIsDueCorrectlyReturnsTrueWhenExpressionIsEmpty()
    {   
        $expression = '';
        $useContactTimezone = null;
        $timezone = null;
        
        /** @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        /** @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead(null);
        
        //call the function
        $isDue = $timingHelper->isDue(1, $lead, 'now');
        
        $this->assertTrue($isDue);
    }
    
    /**
     * Helper function to get a TimingHelper for use by our tests.
     * @return TimingHelper Returns a TimingHelper for use by our tests.
     */
    private function getTimingHelper(Timing $timing)
    {   
        //mock the Event
        $event = $this->getMockBuilder('\Mautic\CampaignBundle\Entity\Event')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //mock the eventModel
        $eventModel = $this->getMockBuilder('\Mautic\CampaignBundle\Model\EventModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the eventModel->getEntity() function
        $eventModel->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($event));

        //mock the timingModel
        $timingModel = $this->getMockBuilder('\MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the timingModel->getEntity() function
        $timingModel->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($timing));
                
        //create the timingHelper
        $timingHelper = new TimingHelper(
                            $eventModel,
                            $timingModel
                        );
        
        return $timingHelper;
    }
    
    /**
     * Helper function to create a mock Timing object for us by our tests.
     * @param string|null $expression The cron expression for the Timing.
     * @param boolean|null $useContactTimezone Whether or not to use the 
     * contact's timezone.
     * @param string|null $timezone The timezone for the Timing
     * @return Timing Returns a mock Timing object.
     */
    private function getMockTiming(
                        $expression,
                        $useContactTimezone = null,
                        $timezone = null
                    )
    {
        //mock the Timing
        $timing = $this->getMockBuilder('\MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the timing->getExpression() function
        $timing->expects($this->any())
            ->method('getExpression')
            ->will($this->returnValue($expression));
        
        //stub the timing->getUseContactTimezone() function
        $timing->expects($this->any())
            ->method('getUseContactTimezone')
            ->will($this->returnValue($useContactTimezone));
        
        //stub the timing->getTimezone() function
        $timing->expects($this->any())
            ->method('getTimezone')
            ->will($this->returnValue($timezone));
        
        return $timing;
    }
    
    /**
     * Helper function that returns a mock lead for use by our tests.
     * @param string $timezone A timezone as a string (ex: "America/New_York"_
     * @return Lead Returns a mock Lead.
     */
    private function getMockLead($timezone = null)
    {   
        //mock the lead
        $lead = $this->getMockBuilder('\Mautic\LeadBundle\Entity\Lead')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        if($timezone != null) {
            //mock an IpAddress
            $ipAddress = $this->getMockBuilder('\Mautic\CoreBundle\Entity\IpAddress')
                                   ->disableOriginalConstructor()
                                   ->getMock();

            $ipDetails = array(
                        'timezone' => $timezone
                    );

            //stub the ipAddress->getIpDetails() function
            $ipAddress->expects($this->once())
                ->method('getIpDetails')
                ->will($this->returnValue($ipDetails));

            $ipAddresses = new \Doctrine\Common\Collections\ArrayCollection(array($ipAddress));

            //stub the lead->getIpAddresses() function
            $lead->expects($this->atLeastOnce())
                ->method('getIpAddresses')
                ->will($this->returnValue($ipAddresses));
        }
        
        return $lead;
    }
}

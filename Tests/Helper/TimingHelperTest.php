<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2017 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Tests\Helper;

use Doctrine\Common\Collections\ArrayCollection;

use MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing;
use MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper;

/**
 * Class TimingHelper test has tests for the TimingHelper class.
 */
class TimingHelperTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @testdox checkEventTiming correctly returns true for a due simple expression.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::isDue
     */
    public function testCheckEventTimingCorrectlyReturnsTrueForADueSimpleExpression()
    {
        //the time and expression would should return true.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming correctly returns DateTime for a not yet due
     * simple expression.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsDateTimeWhenScheduled()
    {
        //the time and expression would should return true.
        $mockNow = '2016-01-01 01:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate instanceof \DateTime);
    }
    
    /**
     * @testdox checkEventTiming correctly returns true when a *lead's* time 
     * zone makes it due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsTrueWhenContactsTimezoneMakesItDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 01-06 * * *';
        $useContactTimezone = true;
        $contactTimezone = 'America/New_York'; // -5
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming correctly returns a DateTime when a *lead's*
     * time zone makes it not due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsDateTimeWhenContactsTimezoneMakesItNotYetDue()
    {
        //the time and expression would return true, but the offset should 
        //cause them to return a DateTime instead.
        //It's 10 AM in London (5 AM New York), don't send until 9 AM in New York.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = true;
        $contactTimezone = 'America/New_York'; // -5
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate instanceof \DateTime);
    }
    
    /**
     * @testdox checkEventTiming correctly returns a DateTime when event tries
     * to use the leads timezone but it isn't known.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsDateTimeWhenContactsTimezoneIsntKnown()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 01-06 * * *';
        $useContactTimezone = true;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead();
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate instanceof \DateTime);
    }
    
    
    
    /**
     * @testdox checkEventTiming correctly returns true when the *Timing's* time
     * zone makes it due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsTrueWhenTimingTimezoneMakesItDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 01-06 * * *';
        $useContactTimezone = false;
        $timezone = 'America/New_York'; // -5
        $contactTimezone = null;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming correctly returns DateTime when the *Timing's* 
     * time zone makes it not due.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsDateTimeWhenTimingTimezoneMakesItNotDue()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $timezone = 'America/New_York'; // -5
        $contactTimezone = null;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        $this->assertTrue($eventTriggerDate instanceof \DateTime);
    }
    
    /**
     * @testdox checkEventTiming correctly returns null when the event doesn't
     * have timing data.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsNullWhenEventDoesntHaveTimingData()
    {   
        //mock the timingModel
        $timingModel = $this->getMockBuilder('\MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the timingModel->getEntity() function
        $timingModel->expects($this->once())
            ->method('getById')
            ->will($this->returnValue(null));
                
        //create the timingHelper
        $timingHelper = new TimingHelper(
                            $timingModel
                        );
        
        /** @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead(null);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                array(),
                                                null,
                                                false,
                                                $lead,
                                                'now'
                                            );
        
        $this->assertNull($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming correctly returns null when the Timing object's
     * expression is empty.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsNullWhenExpressionIsEmpty()
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
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead(null);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                array(),
                                                null,
                                                false,
                                                $lead,
                                                'now'
                                            );
        
        $this->assertNull($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming correctly returns null when the decision path ==
     * 'no' and negatives are not allowed.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingCorrectlyReturnsNullDecisionPathIsNoAndNotAllowNegative()
    {   
        $mockNow = '2016-01-01 10:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $timezone = null;
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        $timezone
                    );
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead(null);
        
        $eventData = array();
        $eventData['id'] = 1;
        $eventData['decisionPath'] = 'no';
        
        $allowNegative = false;
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                $allowNegative,
                                                $lead,
                                                $mockNow
                                            );
        
        $this->assertNull($eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming returns expected DateTime when interval
     * settings are set.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingReturnsExpectedDateTimeWhenTriggerModeIsInterval()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 08:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        $triggerMode = 'interval';
        $triggerInterval = 1;
        $triggerIntervalUnit = 'D';
        $expected = new \DateTime('2016-01-2 09:00:00');
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        null
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        $eventData['triggerMode'] = $triggerMode;
        $eventData['triggerInterval'] = $triggerInterval;
        $eventData['triggerIntervalUnit'] = $triggerIntervalUnit;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        //Assert that the expected DateTime is returned
        $this->assertEquals($expected, $eventTriggerDate);
    }
    
    /**
     * @testdox checkEventTiming returns expected DateTime when interval
     * settings are set.
     *
     * @covers \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper::checkEventTiming
     */
    public function testCheckEventTimingReturnsExpectedDateTimeWhenTriggerModeIsDate()
    {
        //the time and expression would return false, but the offset should 
        //cause them to return true instead.
        $mockNow = '2016-01-01 08:00:00';
        $expression = '* 09-19 * * *';
        $useContactTimezone = false;
        $contactTimezone = null;
        $triggerMode = 'date';
        $triggerDate = new \DateTime('2016-01-05 09:00:00');
        $expected = new \DateTime('2016-01-05 09:00:00');
        
        /* @var $timing \MauticPlugin\ThirdSetMauticTimingBundle\Entity\Timing */
        $timing = $this->getMockTiming(
                        $expression,
                        $useContactTimezone,
                        null
                    );
        
        $eventData = array();
        $eventData['id'] = 1;
        $eventData['triggerMode'] = $triggerMode;
        $eventData['triggerDate'] = $triggerDate;
        
        /* @var $timingHelper \MauticPlugin\ThirdSetMauticTimingBundle\Helper\TimingHelper */
        $timingHelper = $this->getTimingHelper($timing);
        
        /* @var $lead \Mautic\LeadBundle\Entity\Lead */
        $lead = $this->getMockLead($contactTimezone);
        
        //call the function
        $eventTriggerDate = $timingHelper->checkEventTiming(
                                                $eventData,
                                                null,
                                                false,
                                                $lead, 
                                                $mockNow
                                            );
        
        //Assert that the expected DateTime is returned
        $this->assertEquals($expected, $eventTriggerDate);
    }
    
    /**
     * Helper function to get a TimingHelper for use by our tests.
     * @return TimingHelper Returns a TimingHelper for use by our tests.
     */
    private function getTimingHelper(Timing $timing)
    {   
        //mock the timingModel
        $timingModel = $this->getMockBuilder('\MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel')
                               ->disableOriginalConstructor()
                               ->getMock();
        
        //stub the timingModel->getEntity() function
        $timingModel->expects($this->once())
            ->method('getById')
            ->will($this->returnValue($timing));
                
        //create the timingHelper
        $timingHelper = new TimingHelper(
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
        
        //stub the timing->useContactTimezone() function
        $timing->expects($this->any())
            ->method('useContactTimezone')
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

            $ipAddresses = new ArrayCollection(array($ipAddress));

        } else {
            //if the lead's ip isn't known an empty ArrayCollection is returned.
            $ipAddresses = new ArrayCollection();
        }
        
        //stub the lead->getIpAddresses() function
        $lead->expects($this->any())
            ->method('getIpAddresses')
            ->will($this->returnValue($ipAddresses));
        
        return $lead;
    }
}

<?php

/*
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2020 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticTimingBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use MauticPlugin\ThirdSetMauticTimingBundle\Form\Type\TimingType;
use MauticPlugin\ThirdSetMauticTimingBundle\Model\TimingModel;

/**
 * Tests the TimingType class.
 */
class TimingTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox buildForm builds form as expected.
     */
    public function testBuildForm()
    {
        // Set up dependencies.
        $session = new Session();
        $timingModel = new TimingModel();
        $builder = $this->getMockBuilder(FormBuilderInterface::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $builder->expects($spy = $this->any())->method('add');
        $options = array();

        // Instantiate the SUT (System Under Test) class.
        $timingType = new TimingType($session, $timingModel);

        // Assert that the class was instantiated as expected.
        $this->assertEquals(TimingType::class, get_class($timingType));

        // Call the method.
        $timingType->buildForm($builder, $options);

        // Assert that the builder's add method was called as expected.
        $this->assertEquals(3, $spy->getInvocationCount());
    }

}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\toolbox;

use umi\templating\exception\UnexpectedValueException;
use umi\templating\toolbox\factory\HelperFactory;
use utest\TestCase;

/**
 * Class HelperFactoryTest
 */
class HelperFactoryTest extends TestCase
{
    /**
     * @var \umi\templating\extension\helper\IHelperFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new HelperFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateViewHelper()
    {
        $helperClass = 'utest\templating\mock\helper\MockViewHelper';
        $helper = $this->factory->createHelper($helperClass);

        $this->assertInstanceOf($helperClass, $helper);

        $this->assertNotSame($this->factory->createHelper($helperClass), $helper);
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function wrongHelper()
    {
        $this->factory->createHelper('\StdClass');
    }
}
 
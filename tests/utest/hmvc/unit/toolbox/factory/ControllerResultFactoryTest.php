<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\toolbox\factory\ControllerResultFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Тесты фабрики результатов работы контроллера.
 */
class ControllerResultFactoryTest extends HMVCTestCase
{
    /**
     * @var ControllerResultFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ControllerResultFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateComponent()
    {
        $result = $this->factory->createControllerResult('template', ['vars']);
        $this->assertInstanceOf('umi\hmvc\controller\result\IControllerResult', $result);

        $this->assertEquals('template', $result->getTemplate());
        $this->assertEquals(['vars'], $result->getVariables());
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\component\response\IComponentResponse;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\toolbox\factory\ControllerFactory;
use umi\hmvc\toolbox\factory\ModelFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Тесты фабрики контроллеров.
 */
class ControllerFactoryTest extends HMVCTestCase
{
    /**
     * @var ControllerFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ControllerFactory([
            'mock' => 'utest\hmvc\mock\controller\MockModelController'
        ]);
        $this->resolveOptionalDependencies($this->factory);

        $modelFactory = new ModelFactory([
            'mock' => 'utest\hmvc\mock\model\MockBaseModel'
        ]);
        $this->resolveOptionalDependencies($modelFactory);

        $this->factory->setModelFactory($modelFactory);
    }

    public function testController()
    {
        $controller = $this->factory->createController('mock');
        $this->assertInstanceOf('umi\hmvc\controller\IController', $controller);

        $response = $controller($this->getRequest('/'));
        if (!$response instanceof IComponentResponse) {
            throw new \Exception('Invalid mock model controller.');
        }

        $this->assertEquals('mockmock', $response->getContent());
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongControllerName()
    {
        $this->factory->createController('wrong');
    }
}
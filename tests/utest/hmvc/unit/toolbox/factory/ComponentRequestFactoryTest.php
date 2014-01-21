<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\toolbox\factory\ComponentRequestFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Тесты фабрики для создания HTTP запросов компонента.
 */
class ComponentRequestFactoryTest extends HMVCTestCase
{
    /**
     * @var ComponentRequestFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ComponentRequestFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateRequest()
    {
        $request = $this->factory->createComponentRequest('url');
        $this->assertInstanceOf('umi\hmvc\dispatcher\http\IComponentRequest', $request);
        $this->assertEquals('url', $request->getRequestUri());
    }
}
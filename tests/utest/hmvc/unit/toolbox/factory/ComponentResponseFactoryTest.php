<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\toolbox\factory\ComponentResponseFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Тесты фабрки результатов работы компонента.
 */
class ComponentResponseFactoryTest extends HMVCTestCase
{
    /**
     * @var ComponentResponseFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ComponentResponseFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateComponent()
    {
        $response = $this->factory->createComponentResponse();
        $this->assertInstanceOf('umi\hmvc\component\response\IComponentResponse', $response);
    }
}
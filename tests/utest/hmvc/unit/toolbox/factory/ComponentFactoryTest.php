<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\component\IComponentFactory;
use umi\hmvc\toolbox\factory\ComponentFactory;
use utest\TestCase;

/**
 * Тесты фабрики компонента.
 */
class ComponentFactoryTest extends TestCase
{
    /**
     * @var IComponentFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ComponentFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateComponent()
    {
        $component = $this->factory->createComponent([]);
        $this->assertInstanceOf('umi\hmvc\component\IComponent', $component);

        $component = $this->factory->createComponent(['componentClass' => 'utest\hmvc\mock\MockComponent']);
        $this->assertInstanceOf('utest\hmvc\mock\MockComponent', $component);
    }
}

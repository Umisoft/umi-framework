<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\toolbox\factory\MVCLayerFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Тесты фабрики MVC слоев.
 */
class MVCLayerFactoryTest extends HMVCTestCase
{
    /**
     * @var MVCLayerFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new MVCLayerFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testBasic()
    {
        $cf = $this->factory->createControllerFactory([]);
        $this->assertInstanceOf('umi\hmvc\controller\IControllerFactory', $cf);

        $mf = $this->factory->createModelFactory([]);
        $this->assertInstanceOf('umi\hmvc\model\IModelFactory', $mf);

        $view = $this->factory->createView([]);
        $this->assertInstanceOf('umi\hmvc\view\IView', $view);
    }
}
 
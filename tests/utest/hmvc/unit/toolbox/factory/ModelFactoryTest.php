<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox\factory;

use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\toolbox\factory\ModelFactory;
use utest\TestCase;
use utest\hmvc\mock\model\MockBaseModel;
use utest\hmvc\mock\model\MockDependencyModel;
use utest\hmvc\mock\model\MockModel;

/**
 * Тесты фабрки моделей.
 */
class ModelFactoryTest extends TestCase
{
    /**
     * @var ModelFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ModelFactory([
            'baseModel'  => 'utest\hmvc\mock\model\MockBaseModel',
            'model'      => 'utest\hmvc\mock\model\MockModel',
            'dependency' => 'utest\hmvc\mock\model\MockDependencyModel',
            'aware'      => 'utest\hmvc\mock\model\MockAwareModel'
        ]);
        $this->resolveOptionalDependencies($this->factory);

    }

    public function testCreateByName()
    {
        /**
         * @var MockBaseModel $model
         */
        $model = $this->factory->createByName('baseModel');
        $this->assertEquals('mock', $model->getVariable());

        /**
         * @var MockModel $model
         */
        $model = $this->factory->createByName('model');
        $this->assertEquals('model', $model->getVariable());

        /**
         * @var MockDependencyModel $model
         */
        $model = $this->factory->createByName('dependency');
        $this->assertEquals('dependency mock', $model->getVariable());

        /**
         * @var MockDependencyModel $model
         */
        $model = $this->factory->createByName('aware');
        $this->assertEquals('aware mock', $model->getVariable());
    }

    public function testCreateByClass()
    {
        /**
         * @var MockModel $model
         */
        $model = $this->factory->createByClass('utest\hmvc\mock\model\MockModel');
        $this->assertEquals('model', $model->getVariable());
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongModelName()
    {
        $this->factory->createByName('wrong');
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongModelClass()
    {
        $this->factory->createByClass('\StdClass');
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\toolkit\unit\factory;

use umi\config\entity\Config;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\Toolkit;
use utest\TestCase;
use utest\toolkit\mock\TestFactory;

/**
 * Тестирование фабрики
 */
class FactoryTest extends TestCase implements IFactory
{
    use TFactory;

    public $factories = [];

    protected function setUpFixtures()
    {
        $toolkit = new Toolkit();
        $prototypeFactory = new PrototypeFactory($toolkit);

        $toolkit->setPrototypeFactory($prototypeFactory);
        $this->setToolkit($toolkit);
        $this->setPrototypeFactory($prototypeFactory);
    }

    public function testPrototypeFactory()
    {
        $this->assertInstanceOf(
            'umi\toolkit\prototype\IPrototypeFactory',
            $this->getPrototypeFactory(),
            'Ожидается, что у любой фабрики есть фабрика прототипов'
        );

        $this->_prototypeFactory = null;

        $e = null;
        try {
            $this->getPrototypeFactory();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RequiredDependencyException',
            $e,
            'Ожидается, что нельзя получить фабрику прототипов, если она не была внедрена'
        );
    }

    public function testRegisterFactory()
    {
        $this->assertInstanceOf(
            'utest\toolkit\unit\factory\FactoryTest',
            $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory'),
            'Ожидается, что TFactory::registerFactory() вернет себя'
        );
        $e = null;
        try {
            $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается, что нельзя повторно зарегистрировать фабрику'
        );
    }

    public function testHasFactory()
    {
        $this->assertFalse($this->hasFactory('TestFactory'), 'Ожидается, что фабрика еще не зарегистрирована');
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        $this->assertTrue($this->hasFactory('TestFactory'), 'Ожидается, что фабрика зарегистрирована');
    }

    public function testGetFactory()
    {
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');

        $factory = $this->getFactory('TestFactory');
        $this->assertInstanceOf(
            'umi\toolkit\factory\IFactory',
            $factory,
            'Ожидается, что, если фабрика была зарегистрирована, ее можно получить'
        );
        $this->assertTrue(
            $factory === $this->getFactory('TestFactory'),
            'Ожидается, что TFactory::getFactory() всегда возвращает единственный экземпляр фабрики'
        );
    }

    public function testCreateFactory()
    {
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');

        $createdFactory = $this->createFactory('TestFactory');
        $this->assertInstanceOf(
            'umi\toolkit\factory\IFactory',
            $createdFactory,
            'Ожидается, что, если фабрика была зарегистрирована, можно создать ее экземпляр'
        );

        $this->assertFalse(
            $createdFactory === $this->createFactory('TestFactory'),
            'Ожидается, что TFactory::createFactory() всегда возвращает новый экземпляр фабрики'
        );
    }

    public function testChildFactoriesArrayConfig()
    {
        $this->factories = ['TestFactory' => ['option' => 'injectedValue']];
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        /**
         * @var TestFactory $factory
         */
        $factory = $this->getFactory('TestFactory');
        $this->assertEquals(
            'injectedValue',
            $factory->option,
            'Ожидается, что при создании фабрике были внедрены все опции конфигурации'
        );
    }

    public function testChildFactoriesTraversableConfig()
    {
        $factories = ['TestFactory' => ['option' => 'injectedValue']];
        $this->factories = new Config($factories);
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        /**
         * @var TestFactory $factory
         */
        $factory = $this->getFactory('TestFactory');
        $this->assertEquals(
            'injectedValue',
            $factory->option,
            'Ожидается, что при создании фабрике были внедрены все опции конфигурации тулбокса'
        );
    }

    public function testFactoriesWrongConfig()
    {
        $this->factories = 'wrongConfig';
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        $e = null;
        try {
            $this->getFactory('TestFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            '\InvalidArgumentException',
            $e,
            'Ожидается исключение при некорректной конфигурации фабрик тулбокса'
        );
    }

    public function testWrongChildFactory()
    {

        $this->registerFactory('WrongFactory', 'utest\toolkit\mock\TestFactory', ['NonexistentInterface']);
        $e = null;
        try {
            $this->getFactory('WrongFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить фабрику, если не удалось создать ее экземпляр'
        );

        $e = null;
        try {
            $this->createFactory('WrongFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить новый экземпляр фабрики, если его не удалось'
        );

        $e = null;
        try {
            $this->getFactory('NonexistentFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\NotRegisteredException',
            $e,
            'Ожидается, что нельзя получить незарегистрированную фабрику'
        );
    }

}
 
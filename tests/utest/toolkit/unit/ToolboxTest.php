<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\unit;

use umi\config\entity\Config;
use umi\spl\config\TConfigSupport;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;
use umi\toolkit\Toolkit;
use utest\TestCase;

/**
 * Тестирование набора инструментов
 */
class ToolboxTest extends TestCase implements IToolbox
{

    use TToolbox;

    /**
     * Метод для создания специфического окружения тест-кейса.
     * Может быть перегружен в конкретном тест-кейсе, если это необходимо
     */
    protected function setUpFixtures()
    {
        $toolkit = new Toolkit();
        $prototypeFactory = new PrototypeFactory($toolkit);

        $toolkit->setPrototypeFactory($prototypeFactory);
        $this->setToolkit($toolkit);
        $this->setPrototypeFactory($prototypeFactory);
    }

    public function testMethods()
    {
        $e = null;
        try {
            $this->getService(null, null);
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf('umi\toolkit\exception\UnsupportedServiceException', $e);
    }

    public function testFactory()
    {

        $this->assertFalse($this->hasFactory('TestFactory'), 'Ожидается, что фабрика еще не зарегистрирована');

        $this->assertInstanceOf(
            'utest\toolkit\unit\ToolboxTest',
            $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory'),
            'Ожидается, что IToolbox::registerFactory() вернет себя'
        );
        $this->assertTrue($this->hasFactory('TestFactory'), 'Ожидается, что фабрика зарегистрирована');

        $e = null;
        try {
            $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается, что нельзя повторно зарегестрировать фабрику'
        );

        $factory = $this->getFactory('TestFactory');
        $this->assertInstanceOf(
            'umi\toolkit\factory\IFactory',
            $factory,
            'Ожидается, что, если фабрика была зарегестрирована, ее можно получить'
        );
        $this->assertTrue(
            $factory === $this->getFactory('TestFactory'),
            'Ожидается, что существует единственный экземпляр фабрики'
        );
    }

    public function testFactoriesArrayConfig()
    {
        $this->factories = ['TestFactory' => ['option' => 'injectedValue']];
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
        $factory = $this->getFactory('TestFactory');
        $this->assertEquals(
            'injectedValue',
            $factory->option,
            'Ожидается, что при создании фабрике были внедрены все опции конфигурации тулбокса'
        );
    }

    public function testFactoriesTraversableConfig()
    {
        $factories = ['TestFactory' => ['option' => 'injectedValue']];
        $this->factories = new Config($factories);
        $this->registerFactory('TestFactory', 'utest\toolkit\mock\TestFactory');
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

    public function testWrongFactory()
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
            $this->getFactory('NonexistentFactory');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\NotRegisteredException',
            $e,
            'Ожидается, что нельзя получить незарегестрированную фабрику'
        );
    }
}



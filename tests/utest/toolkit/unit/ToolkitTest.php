<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\unit;

use umi\toolkit\IToolkit;
use umi\toolkit\Toolkit;
use utest\TestCase;
use utest\toolkit\mock\MockTools;

/**
 * Тестирование toolkit
 */
class ToolkitTest extends TestCase
{

    /**
     * @var IToolkit $toolkit
     */
    protected $toolkit;
    /**
     * @var array $toolboxConfig
     */
    protected $toolboxConfig;

    protected function setUpFixtures()
    {
        $this->toolkit = new Toolkit();

        $this->toolboxConfig = [
            'name'              => 'MockTools',
            'class'             => 'utest\toolkit\mock\MockTools',
            'awareInterfaces'   => [
                'utest\toolkit\mock\MockServicingInterface'
            ],
            'services'          => [
                'utest\toolkit\mock\IMockService',
                'utest\toolkit\mock\TestFactory'
            ]
        ];
    }

    public function testRegisterToolbox()
    {

        $this->assertFalse(
            $this->toolkit->hasToolbox(MockTools::NAME),
            'Ожидается, что тулбоксменеджер не содержит незарегистрированный тулбокс'
        );

        $e = null;
        try {
            $this->toolkit->registerToolbox('NoArray');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке зарегистрировать тулбокс с некорректной конфигурацией'
        );
        $this->assertEquals('Cannot register toolbox. Invalid configuration.', $e->getMessage());

        $e = null;
        try {
            $this->toolkit->registerToolbox(['NoName']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке зарегистрировать тулбокс с некорректной конфигурацией'
        );
        $this->assertEquals('Cannot register toolbox. Option "name" required.', $e->getMessage());

        $e = null;
        try {
            $this->toolkit->registerToolbox(
                ['name' => MockTools::NAME, 'NoClassName']
            );
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке зарегистрировать тулбокс с некорректной конфигурацией'
        );
        $this->assertEquals('Cannot register toolbox "MockTools". Option "class" required.', $e->getMessage());

        $result = $this->toolkit->registerToolbox($this->toolboxConfig);

        $this->assertInstanceOf(
            'umi\toolkit\IToolkit',
            $result,
            'Ожидается, что Toolkit::registerToolbox() вернет себя'
        );

        $this->assertTrue(
            $this->toolkit->hasToolbox(MockTools::NAME),
            'Ожидается, что тулбоксменеджер содержит зарегистрированный тулбокс'
        );

        $e = null;
        try {
            $this->toolkit->registerToolbox($this->toolboxConfig);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается исключение при повторной попытке зарегистрировать тулбокс'
        );
    }

    public function testRegisterToolboxes()
    {
        $e = null;
        try {
            $this->toolkit->registerToolboxes('NoArray');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке зарегистрировать тулбоксы с некорректной конфигурацией'
        );

        $this->assertInstanceOf(
            'umi\toolkit\IToolkit',
            $this->toolkit->registerToolboxes(
                [
                    [
                        'name'  => MockTools::NAME,
                        'class' => 'utest\toolkit\mock\MockTools',
                        'config'=> []
                    ]
                ]
            ),
            'Ожидается, что IToolkit::registerToolboxes() вернет себя'
        );
    }

    public function testServices()
    {
        $e = null;
        try {
            $this->toolkit->getService('WrongInterface');
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\NotRegisteredException',
            $e,
            'Ожидается исключение, на попытку получить сервис, который не зарегистрирован.'
        );
        $this->assertFalse(
            $this->toolkit->hasService('WrongInterface'),
            'Ожидается, что сервсис не зарегистрирован.'
        );
        $this->assertEmpty(
            $this->toolkit->getInjectors(['WrongInterface'])
        );

        $this->toolkit->registerToolbox($this->toolboxConfig);

        $this->assertTrue(
            $this->toolkit->hasAwareInterface('utest\toolkit\mock\MockServicingInterface')
        );
        $this->assertTrue(
            $this->toolkit->hasService('utest\toolkit\mock\IMockService')
        );

        $this->assertCount(
            1,
            $this->toolkit->getInjectors(
                ['utest\toolkit\mock\MockServicingInterface', 'utest\toolkit\mock\IMockService']
            )
        );

        $e = null;
        try {
            $this->toolkit->registerToolbox(
                [
                    'name'      => 'SomeTools',
                    'class'     => 'utest\toolkit\mock\WrongTools',
                    'services'  => [
                        'utest\toolkit\mock\IMockService'
                    ]
                ]
            );
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается, что нельзя зарегистрировать сервис больше одного раза'
        );

        $e = null;
        try {
            $this->toolkit->registerToolbox(
                [
                    'name'      => 'SomeTools2',
                    'class'     => 'utest\toolkit\mock\WrongTools',
                    'awareInterfaces' => [
                        'utest\toolkit\mock\MockServicingInterface'
                    ],
                ]
            );
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается, что нельзя зарегистрировать инжектор больше одного раза'
        );
    }

    public function testWrongToolbox()
    {
        $this->toolkit->registerToolbox(
            [
                'name'      => 'WrongTools',
                'class'     => 'utest\toolkit\mock\WrongTools',
                'services'  => [
                    'utest\toolkit\mock\IMockService'
                ]
            ]
        );
        $e = null;
        try {
            $this->toolkit->getService('utest\toolkit\mock\IMockService');
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить сервис, зарегистрированный в тулбоксе, не реализующем интерфейс IToolbox'
        );
        $this->assertEquals('Cannot create toolbox "WrongTools".', $e->getMessage());
    }

    public function testGetPrototypeInBuilders()
    {
        $this->toolkit->registerService(
            'utest\toolkit\mock\IMockService',
            function ($concreteClassName, IToolkit $toolkit) {
                $concreteClassName = $concreteClassName ?: 'utest\toolkit\mock\MockService';
                return $toolkit->getPrototype($concreteClassName, ['utest\toolkit\mock\IMockService'])->createInstance();
            }
        );

        $this->assertInstanceOf(
            'utest\toolkit\mock\MockService',
            $this->toolkit->getService('utest\toolkit\mock\IMockService'),
            'Ожидается, что можно получить зарегистрированный сервис с дефолтной реализацией'
        );

        $this->assertInstanceOf(
            'utest\toolkit\mock\ConcreteMockService',
            $this->toolkit->getService('utest\toolkit\mock\IMockService', 'utest\toolkit\mock\ConcreteMockService'),
            'Ожидается, что можно получить зарегистрированный сервис с указанной реализацией'
        );
    }

    public function testSetSettings()
    {
        $this->toolkit->registerToolbox($this->toolboxConfig);
        $this->toolkit->setSettings(
            [
                MockTools::NAME => [
                    'factoryOptions' => ['injectedName']
                ]
            ]
        );

        $this->assertEquals(
            'injectedName',
            $this->toolkit->getService('utest\toolkit\mock\TestFactory')->getName(),
            'Ожидается, что публичные свойства тулбокса можно настроить через IToolkit::setSettings()'
        );

        $e = null;
        try {
            $this->toolkit->setSettings('WrongSettings');
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\InvalidArgumentException',
            $e,
            'Ожидается, что нельзя выставить настройки тулкита в неверном формате'
        );
        $this->assertEquals('Cannot set toolkit settings.', $e->getMessage());

        $e = null;
        try {
            $this->toolkit->setSettings(['ToolboxName' => 'WrongToolboxConfig']);
        } catch (\Exception $e) {}
        $this->assertInstanceOf(
            'umi\toolkit\exception\UnexpectedValueException',
            $e,
            'Ожидается, что нельзя выставить настройки тулкита в неверном формате'
        );
        $this->assertEquals('Cannot set toolbox "ToolboxName" settings.', $e->getMessage());
    }

}

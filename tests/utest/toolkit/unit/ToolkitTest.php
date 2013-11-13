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
            'name'    => 'MockTools',
            'class'        => 'utest\toolkit\mock\MockTools',
            'servicingInterfaces' => [
                'utest\toolkit\mock\MockServicingInterface'
            ],
            'services'            => [
                'utest\toolkit\mock\IMockService'
            ]
        ];
    }

    public function testRegisterToolbox()
    {

        $this->assertFalse(
            $this->toolkit->hasToolbox(MockTools::NAME),
            'Ожидается, что тулбоксменеджер не содержит незарегестрированный тулбокс'
        );

        $result = $this->toolkit->registerToolbox($this->toolboxConfig);

        $this->assertInstanceOf(
            'umi\toolkit\IToolkit',
            $result,
            'Ожидается, что Toolkit::registerToolbox() вернет себя'
        );

        $this->assertTrue(
            $this->toolkit->hasToolbox(MockTools::NAME),
            'Ожидается, что тулбоксменеджер содержит зарегестрированный тулбокс'
        );

        $e = null;
        try {
            $this->toolkit->registerToolbox($this->toolboxConfig);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\AlreadyRegisteredException',
            $e,
            'Ожидается исключение при повторной попытке зарегестрировать тулбокс'
        );
    }

    public function testRegisterToolboxes()
    {
        $e = null;
        try {
            $this->toolkit->registerToolboxes(['NoArray']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке зарегестрировать тулбоксы с некорректной конфигурацией'
        );

        $e = null;
        try {
            $this->toolkit->registerToolboxes([['NoName']]);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке зарегестрировать тулбоксы с некорректной конфигурацией'
        );

        $e = null;
        try {
            $this->toolkit->registerToolboxes(
                [['name' => MockTools::NAME, 'NoClassName']]
            );
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке зарегестрировать тулбоксы с некорректной конфигурацией'
        );

        $this->assertInstanceOf(
            'umi\toolkit\IToolkit',
            $this->toolkit->registerToolboxes(
                [
                    [
                        'name' => MockTools::NAME,
                        'class'     => 'utest\toolkit\mock\MockTools',
                        'config'           => []
                    ]
                ]
            ),
            'Ожидается, что IToolkit::registerToolboxes() вернет себя'
        );
    }

    public function testGetToolbox()
    {

        $e = null;
        try {
            $this->toolkit->getToolbox(MockTools::NAME);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\NotRegisteredException',
            $e,
            'Ожидается, что нельзя получить незарегестрированный тулбокс'
        );

        $this->toolkit->registerToolbox($this->toolboxConfig);

        $toolbox = $this->toolkit->getToolbox(MockTools::NAME);
        $this->assertInstanceOf(
            'utest\toolkit\mock\MockTools',
            $toolbox,
            'Ожидается, что можно получить зарегестрированный тулбокс'
        );
        $this->assertTrue(
            $toolbox === $this->toolkit->getToolbox(MockTools::NAME),
            'Ожидается, что для тулбокса создается только один объект'
        );

        $this->toolkit->registerToolbox(
            [
                'name' => 'WrongTools',
                'class'     => 'utest\toolkit\mock\WrongTools'
            ]
        );
        $e = null;
        try {
            $this->toolkit->getToolbox('WrongTools');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить тулбокс, не реализующий интерфейс IToolbox'
        );
    }

    public function testServices()
    {
        $e = null;
        try {
            $this->toolkit->getService('WrongInterface');
        } catch (\Exception $e) {
        }

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
            $this->toolkit->hasInjector('utest\toolkit\mock\MockServicingInterface')
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
    }
}

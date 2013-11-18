<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\toolkit\unit\prototype;

use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\Toolkit;
use utest\TestCase;

/**
 * Тест фабрики прототипов сервисов.
 */
class PrototypeFactoryTest extends TestCase
{
    /**
     * @var PrototypeFactory $factory
     */
    protected $factory;

    protected function setUpFixtures()
    {
        $toolkit = new Toolkit();
        $this->factory = new PrototypeFactory($toolkit);
    }

    public function testUnsuccessfulCreate()
    {

        $prototypeClassName = $this->factory->prototypeClass;
        $this->factory->prototypeClass = 'utest\toolkit\mock\ServicingMock';

        $e = null;
        try {
            $this->factory->create('utest\toolkit\mock\TestService');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке создать прототип, не реализующей интерфейс IPrototype'
        );
        $this->assertEquals('Prototype class "utest\toolkit\mock\ServicingMock" should implement IPrototype.', $e->getMessage(), 'Неверный текст исключения');

        $this->factory->prototypeClass = $prototypeClassName;

        $e = null;
        try {
            $this->factory->create('WrongClass');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке создать прототип объекта несуществующего класса'
        );
        $this->assertEquals('Class "WrongClass" does not exist.', $e->getMessage(), 'Неверный текст исключения');

        $e = null;
        try {
            $this->factory->create('utest\toolkit\mock\TestService', ['WrongInterface']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке создать прототип объекта класса с проверкой на несуществующий интерфейс'
        );
        $this->assertEquals(
            'Interface or class "WrongInterface" does not exist.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $this->factory->create('utest\toolkit\mock\TestService', ['utest\toolkit\mock\IWrong']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\DomainException',
            $e,
            'Ожидается исключение при попытке создать прототип объекта класса, нереализующего заданный интерфейс'
        );
        $this->assertEquals(
            'Instance of "utest\toolkit\mock\TestService" should implement "utest\toolkit\mock\IWrong".',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testSuccessfulCreate()
    {
        $this->assertInstanceOf(
            'umi\toolkit\prototype\IPrototype',
            $this->factory->create('utest\toolkit\mock\ServicingMock'),
            'Ожидается, что при успешном создании прототипа будет возвращен IPrototype'
        );

        $this->assertInstanceOf(
            'umi\toolkit\prototype\IPrototype',
            $this->factory->create('utest\toolkit\mock\TestService', ['utest\toolkit\mock\ITestService']),
            'Ожидается, что при успешном создании прототипа будет возвращен IPrototype'
        );
    }
}
 
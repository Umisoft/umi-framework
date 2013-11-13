<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\unit;

use stdClass;
use umi\config\entity\Config;
use umi\spl\config\TConfigSupport;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\IToolkit;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\Toolkit;
use utest\TestCase;
use utest\toolkit\mock\MockService;
use utest\toolkit\mock\MockServicingInterface;
use utest\toolkit\mock\TestService;

/**
 * Тест фабрики объектов
 */
class FactoryTest extends TestCase implements IFactory
{

    use TFactory;
    use TConfigSupport;

    /**
     * @var IToolkit $toolkit
     */
    protected $toolkit;

    /**
     * Метод для создания специфического окружения тест-кейса.
     * Может быть перегружен в конкретном тест-кейсе, если это необходимо
     */
    protected function setUpFixtures()
    {
        $this->toolkit = new Toolkit();
        $prototypeFactory = new PrototypeFactory($this->toolkit);
        $this->toolkit->setPrototypeFactory($prototypeFactory);
        $this->setToolkit($this->toolkit);
        $this->setPrototypeFactory($prototypeFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'stdClass':
            {
                return new stdClass();
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function initPrototypeInstance($prototype)
    {
        if ($prototype instanceof MockServicingInterface) {
            $prototype->setFactoryService('injectedDependency');
        }
    }

    public function testCreateInstance()
    {
        $instance = $this->createInstance('utest\toolkit\mock\TestService');
        $this->assertFalse(
            $instance === $this->createInstance('utest\toolkit\mock\TestService'),
            'Ожидается, что TFactory::createInstance() каждый раз создает новые экземпляры'
        );

        $this->assertNull(
            $this->getSingleInstance('utest\toolkit\mock\TestService'),
            'Ожидается, что TFactory::getSingleInstance() вернет null, если объект еще не был создан'
        );
        $singleInstance = $this->createSingleInstance('utest\toolkit\mock\TestService');
        $this->assertTrue(
            $singleInstance === $this->createSingleInstance('utest\toolkit\mock\TestService'),
            'Ожидается, что метод TFactory::createSingleInstance() каждый раз возвращает одну и ту же сущность'
        );
        $this->assertTrue(
            $singleInstance === $this->getSingleInstance('utest\toolkit\mock\TestService'),
            'Ожидается, что метод TFactory::getSingleInstance() вернет ранее созданную сущность'
        );
    }

    public function testContracts()
    {

        $e = null;
        try {
            $this->createInstance('WrongClass');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить объект несуществующего класса'
        );
        $this->assertEquals('Class "WrongClass" does not exist.', $e->getMessage(), 'Неверный текст исключения');

        $e = null;
        try {
            $this->createInstance('utest\toolkit\mock\TestService', [], ['WrongInterface']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить объект, реализующий несуществующий интерфейс'
        );
        $this->assertEquals(
            'Interface or class "WrongInterface" does not exist.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $this->createInstance('utest\toolkit\mock\TestService', [], ['utest\toolkit\mock\IWrong']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\toolkit\exception\DomainException',
            $e,
            'Ожидается исключение при попытке получить объект, нереализующий заданный интерфейс'
        );
        $this->assertEquals(
            'Instance of "utest\toolkit\mock\TestService" should implement "utest\toolkit\mock\IWrong".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->assertInstanceOf(
            'utest\toolkit\mock\ITestService',
            $this->createInstance('utest\toolkit\mock\TestService', [], ['utest\toolkit\mock\ITestService']),
            'Ожидается, что созданный объект прошел проверку контракта'
        );
    }

    public function testInjectOptions()
    {

        $e = null;
        try {
            $this->createInstance('utest\toolkit\mock\TestService', [], [], ['options' => 'WrongValue']);
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf(
            '\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке установить в опцию, заданную массивом, не массив'
        );
        $this->assertEquals(
            'Cannot resolve option "options". Option value should be of type array.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $this->createInstance('utest\toolkit\mock\TestService', [], [], ['name' => ['WrongValue']]);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            '\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке установить в опцию, заданную скаляром, не скаляр'
        );
        $this->assertEquals(
            'Cannot resolve option "name". Option value should be of type string.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $this->createInstance('utest\toolkit\mock\TestService', [], [], ['options' => ['a3' => []]]);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            '\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке установить в опцию, заданную скаляром, не скаляр'
        );
        $this->assertEquals(
            'Cannot resolve option "a3". Option value should be of type integer.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        /**
         * @var TestService $testService
         */
        $testService = $this->createInstance(
            'utest\toolkit\mock\TestService',
            [],
            [],
            [
                'object'  => new stdClass(),
                'name'    => 'TestService',
                'options' => [
                    4,
                    'a3' => 5,
                    'a6' => 6,
                    'a4' => [
                        'a4' => 4,
                        'a5' => 777
                    ]
                ]
            ]
        );

        $this->assertInstanceOf('stdClass', $testService->object, 'Ожидается внедрение объекта.');
        $this->assertEquals(
            'TestService',
            $testService->name,
            'Ожидается, что при создании объекта ему были внедрены его публичные свойства'
        );
        $this->assertEquals(
            [
                'a1' => 1,
                'a2' => 2,
                'a3' => 5,
                'a4' => ['a5' => 777, 'a6' => 6, 'a4' => 4],
                0    => 4,
                'a6' => 6
            ],
            $testService->options,
            'Ожидается, что свойства заданные массивом будут смерджены'
        );

        $data = ['options' => ['a3' => 5, 'a6' => 6, 'a4' => ['a4' => 4, 'a5' => 777]]];
        $testService = $this->createInstance(
            'utest\toolkit\mock\TestService',
            [],
            [],
            $this->configToArray(new Config($data))
        );
        $this->assertEquals(
            ['a1' => 1, 'a2' => 2, 'a3' => 5, 'a4' => ['a5' => 777, 'a6' => 6, 'a4' => 4], 'a6' => 6],
            $testService->options,
            'Неверный итоговый конфиг'
        );
    }

    public function testInterfaceDependencies()
    {
        $this->toolkit->registerToolbox(
            [
                'name'    => 'MockTools',
                'class'        => 'utest\toolkit\mock\MockTools',
                'awareInterfaces' => [
                    'utest\toolkit\mock\MockServicingInterface',
                    'utest\toolkit\mock\IMockService'
                ]
            ]
        );

        /**
         * @var TestService $testService
         */
        $testService = $this->createInstance('utest\toolkit\mock\TestService');

        $this->assertEquals(
            'injectedDependency',
            $testService->dependency,
            'Ожидается, что тулбокс, обслуживающий интерфейс класса, внедрит соответствующие зависимости'
        );
        $this->assertEquals(
            'injectedDependency',
            $testService->factoryService,
            'Ожидается, что при создании объекта будет выполнен инициализатор прототипа'
        );
    }

    public function testWakeUpObject()
    {

        $this->toolkit->registerToolbox(
            [
                'name'    => 'MockTools',
                'class'        => 'utest\toolkit\mock\MockTools',
                'awareInterfaces' => [
                    'utest\toolkit\mock\MockServicingInterface'
                ]
            ]
        );

        $testService = new TestService();
        $this->assertNull(
            $testService->dependency,
            'Ожидается, что при создании объекта через new у него не было зависисмостей'
        );

        $this->assertTrue(
            $testService === $this->wakeUpInstance($testService),
            'Ожидается, что восстановление объекта не создает нового объекта'
        );
        $this->assertEquals(
            'injectedDependency',
            $testService->dependency,
            'Ожидается, что восстановление объекта восстановит все его зависимости'
        );
        $this->assertEquals(
            'injectedDependency',
            $testService->factoryService,
            'Ожидается, что восстановление объекта восстановит все его зависимости'
        );
    }

    public function testConstructor()
    {
        $this->toolkit->registerToolbox(
            [
                'name'    => 'MockTools',
                'class'        => 'utest\toolkit\mock\MockTools',
                'services'         => [
                    'utest\toolkit\mock\IMockService'
                ]
            ]
        );

        $reference = 1;
        /**
         * @var TestService $testService
         */
        $testService = $this->createInstance('utest\toolkit\mock\TestService', ['TestService', null, &$reference]);
        $this->assertEquals(
            'TestService',
            $testService->type,
            'Ожидается, что при создании объекта ему были переданы параметры конструктора'
        );
        $this->assertInstanceOf(
            'utest\toolkit\mock\IMockService',
            $testService->mockService,
            'Ожидается, что дефолтные значения сервисов были взяты из менеджера тулбокосов'
        );
        $this->assertInstanceOf(
            'utest\toolkit\mock\ConcreteMockService',
            $testService->concreteMockService,
            'Ожидается, что конкретные реализации зарегестрированных абстрактных классов тулюокса были внедрены'
        );
        $this->assertEquals(
            2,
            $reference,
            'Ожидается, что значение переданное по ссылке было принято по ссылки и изменено'
        );

        $testService = $this->createInstance(
            'utest\toolkit\mock\TestService',
            ['TestService', new MockService('NotDefaultName')]
        );
        $this->assertInstanceOf(
            'utest\toolkit\mock\IMockService',
            $testService->mockService,
            'Ожидается, что значения конструктора были инъецированы'
        );
        $this->assertEquals(
            'NotDefaultName',
            $testService->mockService->getName(),
            'Ожидается, что указанный сервис перекрыл дефолтный сервис тулкита.'
        );

        $mockService = new MockService('NotDefaultName');
        $testService = $this->createInstance(
            'utest\toolkit\mock\TestService',
            [null, null, $reference, $mockService, null, null, null, 1, 2, 3]
        );
        $this->assertEquals(
            [1, 2, 3],
            $testService->args,
            'Ожидается, что были приняты все дополнительные параметры конструктора'
        );
        $this->assertEquals(
            'referenceName',
            $mockService->getName(),
            'Ожидается, что параметр был изменен на конструкторе'
        );

    }
}


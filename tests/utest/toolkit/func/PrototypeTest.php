<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\toolkit\func;

use stdClass;
use umi\config\entity\Config;
use umi\spl\config\TConfigSupport;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\IToolkit;
use umi\toolkit\prototype\IPrototype;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\Toolkit;
use utest\TestCase;
use utest\toolkit\mock\MockService;
use utest\toolkit\mock\MockServicingInterface;
use utest\toolkit\mock\TestService;

/**
 * Тест создания объектов на основе прототипов
 */
class PrototypeTest extends TestCase implements IFactory
{

    use TFactory;

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


    public function testGetClass()
    {
        $prototype = $this->getPrototype('utest\toolkit\mock\TestService');
        $this->assertEquals(
            'utest\toolkit\mock\TestService',
            $prototype->getClassName(),
            'Ожидается, что прототип хранит в себе имя класса создаваемых объектов'
        );
    }

    public function testConstructorArgsInjection()
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
        $testService =
            $this->getPrototype('utest\toolkit\mock\TestService')
                ->createInstance(['TestService', null, &$reference]);

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
            'Ожидается, что конкретные реализации зарегестрированных абстрактных классов тулбокса были внедрены'
        );
        $this->assertEquals(
            2,
            $reference,
            'Ожидается, что значение переданное по ссылке было принято по ссылки и изменено'
        );

        $testService =
            $this->getPrototype('utest\toolkit\mock\TestService')
                ->createInstance(['TestService', new MockService('NotDefaultName')]);

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
        $testService =
            $this->getPrototype('utest\toolkit\mock\TestService')
                ->createInstance([null, null, $reference, $mockService, null, null, null, 1, 2, 3]);

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

    public function testRegisterConstructorDependency()
    {

        $prototype = $this->getPrototype('utest\toolkit\mock\TestService');

        $prototype->registerConstructorDependency(
            'utest\toolkit\mock\IMockService',
            function ($concreteClassName) {
                $concreteClassName = $concreteClassName ?: 'utest\toolkit\mock\MockService';
                return new $concreteClassName('injectedByPrototype');
            }
        );

        /**
         * @var TestService $testService
         */
        $testService = $prototype->createInstance();

        $this->assertEquals(
            'injectedByPrototype',
            $testService->mockService->getName(),
            'Ожидается, что в объект были внедрены зависимости, зарегестрированные в прототипе'
        );
    }

    public function testWrongOptionInjection()
    {

        $prototype = $this->getPrototype('utest\toolkit\mock\TestService');
        $instance = $prototype->createInstance();

        $e = null;
        try {
            $prototype->setOptions($instance, ['options' => 'WrongValue']);
        } catch (\Exception $e) { }

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
            $prototype->setOptions($instance, ['name' => ['WrongValue']]);
        } catch (\Exception $e) { }

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
            $prototype->setOptions($instance, ['options' => ['a3' => []]]);
        } catch (\Exception $e) { }

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
    }

    public function testInjectOptions()
    {

        $prototype = $this->getPrototype('utest\toolkit\mock\TestService');

        /**
         * @var TestService $testService
         */
        $testService = $prototype->createInstance();
        $prototype->setOptions(
            $testService,
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
            'Ожидается, что при выставлении опций объекту ему были внедрены его публичные свойства'
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
        $testService = $prototype->createInstance();
        $prototype->setOptions(
            $testService,
            $this->configToArray(new Config($data))
        );
        $this->assertEquals(
            ['a1' => 1, 'a2' => 2, 'a3' => 5, 'a4' => ['a5' => 777, 'a6' => 6, 'a4' => 4], 'a6' => 6],
            $testService->options,
            'Неверный итоговый конфиг'
        );

        /**
         * @var TestService $testService
         */
        $testService = $prototype->createInstance([], ['name' => 'CreateInstanceOptionService']);
        $this->assertEquals(
            'CreateInstanceOptionService',
            $testService->name,
            'Ожидается, что при создании объекта ему были внедрены его публичные свойства'
        );

    }

    public function testInterfaceDependencies()
    {
        $this->toolkit->registerToolbox(
            [
                'name'    => 'MockTools',
                'class'   => 'utest\toolkit\mock\MockTools',
                'awareInterfaces' => [
                    'utest\toolkit\mock\MockServicingInterface',
                    'utest\toolkit\mock\IMockService'
                ]
            ]
        );

        $prototype = $this->getPrototype(
            'utest\toolkit\mock\TestService',
            [],
            function (IPrototype $prototype)
            {
                $prototypeInstance = $prototype->getPrototypeInstance();
                if ($prototypeInstance instanceof MockServicingInterface) {
                    $prototypeInstance->setInitializerService('injectedDependency');
                }
            }
        );
        /**
         * @var TestService $testService
         */
        $testService = $prototype->createInstance();

        $this->assertEquals(
            'injectedDependency',
            $testService->dependency,
            'Ожидается, что тулбокс, обслуживающий интерфейс класса, внедрит соответствующие зависимости'
        );
        $this->assertEquals(
            'injectedDependency',
            $testService->initializerService,
            'Ожидается, что при создании объекта будет выполнен инициализатор прототипа'
        );
    }

    public function testCreateSingleInstance()
    {
        $prototype = $this->getPrototype('utest\toolkit\mock\TestService');
        /**
         * @var TestService $singleInstance
         */
        $singleInstance = $prototype->createSingleInstance(
            ['TestType'],
            ['name' => 'TestName'],
            function (TestService $testService) {
                $testService->setService('TestService');
            }
        );

        $this->assertTrue(
            $singleInstance === $prototype->createSingleInstance(
                ['SecondTestType'],
                ['name' => 'SecondTestName'],
                function (TestService $testService) {
                    $testService->setService('SecondTestService');
                }
            ),
            'Ожидается, что вызов метода IPrototype::createSingleInstance() всегда работает с единственным экземпляром'
        );

        $this->assertEquals(
            'TestType',
            $singleInstance->type,
            'Ожидается, что при создании объекта ему были внедрены аргументы конструктора'
        );

        $this->assertEquals(
            'TestName',
            $singleInstance->name,
            'Ожидается, что при создании объекта ему были внедрены его публичные свойства'
        );

        $this->assertEquals(
            'TestService',
            $singleInstance->service,
            'Ожидается, что при создании объекта были выполнен инициализатор'
        );
    }

    public function testWakeUpObject()
    {
        $this->toolkit->registerToolbox(
            [
                'name'   => 'MockTools',
                'class'  => 'utest\toolkit\mock\MockTools',
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

        $wrongPrototype = $this->getPrototype('utest\toolkit\mock\MockService');
        $e = null;
        try {
            $wrongPrototype->wakeUpInstance($testService);
        } catch (\Exception $e) { }

        $this->assertInstanceOf(
            'umi\toolkit\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке восстановить объект через "чужой" прототип'
        );
        $this->assertEquals(
            'Cannot wake up object "utest\toolkit\mock\TestService". Object should be instance of "utest\toolkit\mock\MockService".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $prototype = $this->getPrototype('utest\toolkit\mock\TestService', ['utest\toolkit\mock\MockServicingInterface']);
        $prototype->wakeUpInstance($testService);

        $this->assertEquals(
            'injectedDependency',
            $testService->dependency,
            'Ожидается, что восстановление объекта восстановит его зависимости, определенные aware-интерфейсами'
        );

        $this->assertNull(
            $testService->initializerService,
            'Ожидается, что зависимости фабрики еще не были выставлены'
        );

    }
}
 
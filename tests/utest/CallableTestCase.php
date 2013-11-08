<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Тест кейс для callable классов.
 */
abstract class CallableTestCase extends TestCase
{

    /**
     * Отрицательное целое.
     */
    const NEGATIVE_INT = -10;
    /**
     * Ноль.
     */
    const ZERO = 0;
    /**
     * Положительное целое.
     */
    const POSITIVE_INT = 10;
    /**
     * Отрицательное с плавающей точкой.
     */
    const NEGATIVE_FLOAT = -11.5;
    /**
     * Положтиельное с плавающей точкой.
     */
    const POSITIVE_FLOAT = 11.5;
    /**
     * Строка.
     */
    const STRING = 'abrakadabra';
    /**
     * @var Object $callable экземпляр вызываемого класса.
     */
    protected $callable;
    /**
     * @var ReflectionMethod $method тестируемый метод.
     */
    protected $method;
    /**
     * @var array $initArguments инициализованные аргументы для передачи в вызываемую функцию.
     */
    private $initArguments;

    /**
     * Возвращает массив включающий экземпляр тестируемого класса, тестируемый метод класса и набор типизированных параметров.
     * @return array массив формата [$object, 'methodName', ['initObjectClassName' => $initObject...]]
     */
    abstract protected function getCallable();

    /**
     * Проверка передачи в тестируемую функцию аргументов положительных целых значений.
     */
    public function testNegativeIntegerParameters()
    {
        $parameters = $this->buildTestParametersArray(self::NEGATIVE_INT);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * Проверка передачи в тестируемую функцию аргументов нулевых значений.
     */
    public function testZeroParameters()
    {
        $parameters = $this->buildTestParametersArray(self::ZERO);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * Проверка передачи в тестируемую функцию аргументов отрицательных целых значений.
     */
    public function testPositiveIntParameters()
    {
        $parameters = $this->buildTestParametersArray(self::POSITIVE_INT);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * Проверка передачи в тестируемую функцию аргументов отрицательных значений с плавающей точкой.
     */
    public function testNegativeFloatParameters()
    {
        $parameters = $this->buildTestParametersArray(self::NEGATIVE_FLOAT);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * Проверка передачи в тестируемую функцию аргументов положительных значений с плавающей точкой.
     */
    public function testPositiveFloatParameters()
    {
        $parameters = $this->buildTestParametersArray(self::POSITIVE_FLOAT);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * Проверка передачи в тестируемую функцию аргументов строк.
     */
    public function testStringParameters()
    {
        $parameters = $this->buildTestParametersArray(self::STRING);
        $this->callTestFunctionWithParameters($parameters);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $options = $this->getCallable();
        $this->callable = $options[0];
        $this->initArguments = $options[2];
        $reflection = new ReflectionClass(get_class($options[0]));
        $this->method = $reflection->getMethod($options[1]);
    }

    /**
     * Создает массив параметров для передачи их в вызываемую функцию.
     * @param mixed $testValue текущее тестовое значение.
     * @return array массив параметров.
     */
    private function buildTestParametersArray($testValue)
    {
        $parameters = [];
        /**
         * @var ReflectionParameter $parameter
         */
        foreach ($this->method->getParameters() as $parameter) {
            if ($parameter->getClass() === null && !$parameter->isArray()) {
                $parameters[] = $testValue;
            } elseif ($parameter->isArray()) {
                $parameters[] = [];
            } else {
                $parameters[] = isset($this->initArguments[$parameter->getClass()
                    ->getName()]) ?
                    $this->initArguments[$parameter->getClass()
                        ->getName()] : null;
            }
        }

        return $parameters;
    }

    /**
     * Тестирует метод
     * @param array $parameters массив параметров, передаваемый в тестируемый метод.
     */
    private function callTestFunctionWithParameters($parameters)
    {
        try {
            $this->method->invokeArgs($this->callable, $parameters);
        } catch (\Exception $e) {
            $this->assertNotInstanceOf(
                'PHPUnit_Framework_Error',
                $e,
                'Ожидается исключнеие при передаче неверного типа параметра.'
            );
        }
    }
}

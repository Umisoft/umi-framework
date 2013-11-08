<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest;

/**
 * TestCase для написания тестов Aware интерфейсов.
 */
abstract class AwareTestCase extends TestCase
{

    /**
     * Тестирует методы aware интерфейса.
     * @param string $className имя класса
     * @param string $exceptionClass имя класса исключения
     * @param string $message текст исключение, возникающего, если зависимость не была внедрена
     * @throws \Exception если тестирование невозможно
     */
    public function awareClassTest($className, $exceptionClass, $message)
    {
        $class = new \ReflectionClass($className);
        $object = $class->newInstance();

        foreach ($class->getMethods() as $method) {
            if ($method->isProtected()) {
                $method->setAccessible(1);
                $params = $this->resolveMethodParams($method);

                $e = null;
                try {
                    $method->invokeArgs($object, $params);
                } catch (\Exception $e) {
                }
                $this->assertInstanceOf(
                    $exceptionClass,
                    $e,
                    'Ожидается, что будет брошено исключение ' . $exceptionClass
                );

                $this->assertEquals(
                    $message,
                    $e->getMessage(),
                    'Неверный текст исключения при ошибке метода ' . $className . '::' . $method->getName()
                );

            }
        }
    }

    /**
     * Тестирует успешное внедрение сервисов для aware интерфейсов.
     * @param string $className имя тестируемого класса
     * @param string $injectedInterfaceName интерфейс внедряемого сервиса
     * @throws \Exception если тестирование невозможно
     */
    public function successfulInjectionTest($className, $injectedInterfaceName)
    {
        $object = new $className;
        $this->resolveOptionalDependencies($object);

        if (!$object instanceof IMockAware) {
            throw new \Exception(sprintf(
                'Невозможно протестировать %s на внедрение %s. Необходим интерфейс IMockAware у mock класса.',
                $className,
                $injectedInterfaceName
            ));
        }

        $this->assertInstanceOf(
            $injectedInterfaceName,
            $object->getService(),
            sprintf('Сервис %s не был внедрен для %s', $injectedInterfaceName, $className)
        );
    }

    /**
     * Возвращает значение параметров для вызова метода.
     * @param \ReflectionMethod $method метод
     * @return array параметры
     */
    protected function resolveMethodParams(\ReflectionMethod $method)
    {
        $params = [];

        foreach ($method->getParameters() as $param) {
            $name = $param->getName();

            if ($param->isDefaultValueAvailable()) {
                $params[$name] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $params[$name] = null;
            } elseif ($param->isArray()) {
                $params[$name] = [];
            } elseif (!$param->getClass()) {
                $params[$name] = null;
            } else {
                $params[$name] = $this->resolveParam(
                    $param->getClass()
                        ->getName()
                );
            }
        }

        return $params;
    }

    /**
     * Возвращает объект заданного класса или интерфейса.
     * @param string $className
     * @throws \RuntimeException
     * @return mixed
     */
    protected function resolveParam($className)
    {
        throw new \RuntimeException(sprintf("Cannot create class '%s'.", $className));
    }

}
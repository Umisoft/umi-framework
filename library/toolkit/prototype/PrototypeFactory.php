<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\prototype;

use ReflectionClass;
use ReflectionProperty;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\IToolkit;

/**
 * Фабрика прототипов сервисов.
 */
class PrototypeFactory implements IPrototypeFactory
{
    /**
     * @var string $prototypeClass имя класса для создания прототипа
     */
    public $prototypeClass = 'umi\toolkit\prototype\Prototype';
    /**
     * @var IToolkit $toolkit
     */
    protected $toolkit;

    public function __construct(IToolkit $toolkit)
    {
        $this->toolkit = $toolkit;
    }

    /**
     * {@inheritdoc}
     */
    public function create($className, array $contracts = [])
    {
        if (!class_exists($className)) {
            throw new RuntimeException(sprintf('Class "%s" does not exist.', $className));
        }

        $prototype = $this->createWithReflection($className);
        $prototype->setContracts($contracts);
        $prototype->checkContracts($prototype->getPrototypeInstance());

        $prototype->setToolkit($this->toolkit);

        return $prototype;
    }

    /**
     * Создает прототип через Reflection.
     * @param string $className имя класса
     * @throws RuntimeException если не удалось создать прототип
     * @return IPrototype
     */
    private function createWithReflection($className)
    {
        $class = new ReflectionClass($className);
        $prototypeInstance = $class->newInstanceWithoutConstructor();

        /**
         * @var IPrototype $prototype
         */
        $prototype = new $this->prototypeClass(
            $prototypeInstance,
            $class->getInterfaceNames(),
            $this->getClassConstructorInfo($class),
            $this->getClassOptions($class, $prototypeInstance)
        );

        if (!$prototype instanceof IPrototype) {
            throw new RuntimeException(sprintf(
                'Prototype class "%s" should implement IPrototype.',
                $this->prototypeClass
            ));
        }

        return $prototype;
    }

    /**
     * Собирает массив опций класса (публичные свойства).
     * @param ReflectionClass $class
     * @param object $prototypeInstance экземпляр прототипа
     * @return array
     */
    private function getClassOptions(ReflectionClass $class, $prototypeInstance)
    {
        $options = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $options[$property->name] = $property->getValue($prototypeInstance);
        }

        return $options;
    }

    /**
     * Возвращает информацию о конструкторе класса
     * @param ReflectionClass $class класс
     * @return array|null
     */
    private function getClassConstructorInfo(ReflectionClass $class)
    {
        if ($constructor = $class->getConstructor()) {
            $args = [];
            foreach ($constructor->getParameters() as $param) {
                $contracts = [];
                $concreteClassName = null;

                $contract = $param->getClass();
                if ($contract instanceof ReflectionClass) {
                    if (!$contract->isInterface()) {
                        $concreteClassName = $contract->getName();
                    }
                    $contracts = array_merge([$contract->getName()], $contract->getInterfaceNames());
                }

                $args[] = [
                    $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                    $contracts,
                    $concreteClassName
                ];
            }

            return [$constructor->name, $args];
        }

        return null;
    }

}

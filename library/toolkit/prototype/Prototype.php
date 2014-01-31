<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\prototype;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\log\ILoggerAware;
use umi\log\TLoggerAware;
use umi\spl\config\TConfigSupport;
use umi\toolkit\exception\DomainException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\TToolkitAware;

/**
 * Прототип объекта
 */
class Prototype implements IPrototype, ILoggerAware, ILocalizable
{
    use TToolkitAware;
    use TLoggerAware;
    use TConfigSupport;
    use TLocalizable;

    /**
     * @var string $className имя класса для создания прототипа
     */
    protected $className;
    /**
     * @var object $prototypeInstance экземпляр прототипа
     */
    protected $prototypeInstance;
    /**
     * @var object $singleInstance единственный экземпляр
     */
    protected $singleInstance;
    /**
     * @var array $options массив опций прототипа (публичные свойства) в формате [name => defaultValue, ...]
     */
    protected $options = [];
    /**
     * @var bool $hasConstructor есть ли конструктор у прототипа
     */
    protected $hasConstructor = false;
    /**
     * @var string $constructorName имя конструктора
     */
    protected $constructorName;
    /**
     * @var array $constructorArgs аргументы конструктора в формате [[$defaultValue, [$contract1, $contract2, ...], ...]
     */
    protected $constructorArgsInfo = [];
    /**
     * @var array $contracts контракты, которым должен следовать прототип и экземпляры, созданные на его основе
     */
    protected $contracts = [];
    /**
     * @var array $interfaceNames список интерфейсов, которые имплементирует прототип
     */
    private $interfaceNames = [];
    /**
     * @var callable[] $injectors
     */
    private $injectors = [];
    /**
     * @var array $defaultConstructorArgs
     */
    private $defaultConstructorArgs = [];
    /**
     * @var callable[] $constructorDependencyBuilders
     */
    private $constructorDependencyBuilders = [];

    /**
     * Конструктор.
     * @param object $prototypeInstance экземпляр прототипа
     * @param array $interfaceNames список интерфейсов, которые имплементирует прототип
     * @param array $constructorInfo информация о конструкторе
     * @param array $options массив опций прототипа (публичные свойства) в формате [name => defaultValue, ...]
     */
    public function __construct(
        $prototypeInstance,
        array $interfaceNames,
        array $constructorInfo = null,
        array $options
    )
    {
        $this->prototypeInstance = $prototypeInstance;
        $this->interfaceNames = $interfaceNames;
        $this->options = $options;
        $this->className = get_class($prototypeInstance);

        if (is_array($constructorInfo) && !empty($constructorInfo)) {
            $this->hasConstructor = true;
            list($this->constructorName, $this->constructorArgsInfo) = $constructorInfo;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContracts(array $contracts)
    {
        $this->contracts = $contracts;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDependencies()
    {
        $this->trace('Resolve prototype "{class}" dependencies.', ['class' => $this->className]);

        $this->injectors = $this->getToolkit()
            ->getInjectors($this->interfaceNames);
        $this->injectObjectDependencies($this->prototypeInstance);
    }

    /**
     * Возвращает имя класса прототипа.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance(array $constructorArgs = [], array $options = [])
    {
        $instance = clone $this->prototypeInstance;
        $this->invokeConstructor($instance, $constructorArgs);
        if ($options) {
            $this->setOptions($instance, $options);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function createSingleInstance(array $constructorArgs = [], array $options = [], callable $initializer = null)
    {
        if (is_null($this->singleInstance)) {
            $singleInstance = $this->prototypeInstance;
            $this->invokeConstructor($singleInstance, $constructorArgs);
            if ($options) {
                $this->setOptions($singleInstance, $options);
            }
            if (is_callable($initializer)) {
                call_user_func_array($initializer, [$singleInstance]);
            }
            $this->singleInstance = $singleInstance;
        }
        return $this->singleInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function registerConstructorDependency($contract, callable $builder)
    {
        $this->constructorDependencyBuilders[$contract] = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function invokeConstructor($object, array $constructorArgs = [])
    {
        if ($this->hasConstructor) {
            $argNum = 0;
            $invokeArgs = [];
            for ($i = $argNum; $i < count($constructorArgs); $i++) {
                $invokeArgs[$i] = & $constructorArgs[$i];
                if (is_null($invokeArgs[$i])) {
                    $defaultArg = $this->getDefaultConstructorArg($i);
                    if (!is_null($defaultArg)) {
                        $invokeArgs[$i] = $defaultArg;
                    }
                }
                $argNum++;
            }

            $defaultArgs = [];
            for ($i = $argNum; $i < count($this->constructorArgsInfo); $i++) {
                $defaultArgs[$i] = $this->getDefaultConstructorArg($i);
                $invokeArgs[$i] = & $defaultArgs[$i];
            }

            call_user_func_array([$object, $this->constructorName], $invokeArgs);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($object, array $options)
    {
        if (!empty($this->options) && !empty($options)) {
            $overrideOptions = $this->mergeConfigOptions($options, $this->options);
            foreach ($overrideOptions as $optionName => $value) {
                $object->{$optionName} = $value;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function wakeUpInstance($object)
    {
        $objectClassName = get_class($object);

        $this->trace('Wake up object "{class}".', ['class' => $objectClassName]);

        if (!$object instanceof $this->className) {
            throw new RuntimeException($this->translate(
                'Cannot wake up object "{class}". Object should be instance of "{contract}".',
                ['class' => $objectClassName, 'contract' => $this->className]
            ));
        }

        $this->injectObjectDependencies($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrototypeInstance()
    {
        return $this->prototypeInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function checkContracts($object)
    {
        foreach ($this->contracts as $contract) {
            if (!interface_exists($contract) && !class_exists($contract)) {
                throw new RuntimeException(sprintf('Interface or class "%s" does not exist.', $contract));
            }
            if (!$object instanceof $contract) {
                throw new DomainException(sprintf(
                    'Instance of "%s" should implement "%s".',
                    get_class($object),
                    $contract
                ));
            }
        }

        return $this;
    }

    /**
     * Внедряет сервисы в объект.
     * @param object $object
     */
    protected function injectObjectDependencies($object)
    {
        if (count($this->injectors)) {
            $class = get_class($object);
            foreach ($this->injectors as $interface => $injector) {
                $this->trace(
                    'Call injector for "{class}" for support interface "{interface}".',
                    [
                        'class' => $class,
                        'interface' => $interface
                    ]
                );
                call_user_func($injector, $object, $this->getToolkit());
            }
        }
    }

    /**
     * Возвращает значение аргумента конструткора по умолчанию.
     * Возвращает сервисы, известные toolkit.
     * @param int $num номер аргумента
     * @return mixed
     */
    protected function getDefaultConstructorArg($num)
    {
        if (!isset($this->defaultConstructorArgs[$num])) {
            $defaultValue = $serviceBuilder = $concreteClassName = null;

            if (isset($this->constructorArgsInfo[$num])) {
                list($defaultValue, $contracts, $concreteClassName) = $this->constructorArgsInfo[$num];

                $serviceBuilder = null;
                if (count($contracts)) {
                    $serviceBuilder = $this->findServiceBuilderByContracts($contracts) ? : $this->getToolkit()
                        ->findServiceBuilderByContracts($contracts);
                }
            }

            $this->defaultConstructorArgs[$num] = [$defaultValue, $serviceBuilder, $concreteClassName];
        }

        list ($defaultValue, $serviceBuilder, $concreteClassName) = $this->defaultConstructorArgs[$num];

        if (!is_null($serviceBuilder)) {
            $defaultValue = $serviceBuilder($concreteClassName, $this->getToolkit());
        }

        return $defaultValue;
    }

    /**
     * Возвращает билдер сервиса, который подходит под первый из указанных контрактов
     * @param array $contracts список контрактов
     * @return null|callable билдер сервиса, либо null если билдер не найден
     */
    protected function findServiceBuilderByContracts($contracts)
    {
        if (count($this->constructorDependencyBuilders)) {
            foreach ($contracts as $contract) {
                if (isset($this->constructorDependencyBuilders[$contract])) {
                    return $this->constructorDependencyBuilders[$contract];
                }
            }
        }

        return null;
    }

}

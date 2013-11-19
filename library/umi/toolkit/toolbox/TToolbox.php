<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\toolbox;

use Traversable;
use umi\log\TLoggerAware;
use umi\spl\config\TConfigSupport;
use umi\toolkit\exception\AlreadyRegisteredException;
use umi\toolkit\exception\NotRegisteredException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\exception\UnexpectedValueException;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\IToolkitAware;
use umi\toolkit\prototype\IPrototype;

/**
 * Трейт для реализации набора инструментов.
 * Предоставляет функционал для регистрации и конфигурирования фабрик для создания объектов.
 */
trait TToolbox
{

    use TFactory;
    use TConfigSupport;

    /**
     * @var array|Traversable $factories конфигурация фабрик, пробрасывается в конкретную фабрику автоматически
     */
    public $factories = [];
    /**
     * @var array $_registeredFactories список зарегистрированных фабрик
     */
    private $_registeredFactories = [];
    /**
     * @var array $_factoryInstances экземпляры созданных фабрик
     */
    private $_factoryInstances = [];

    /**
     * Возвращает сервис из набора по указанному интерфейсу.
     * @param string $serviceInterfaceName контракт сервиса
     * @param string $concreteClassName имя класса конкретной реализации, может быть использовано
     * для создания нового экземпляра сервиса.
     * @throws UnsupportedServiceException если сервис не поддерживается
     * @return object
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        throw new UnsupportedServiceException(
            sprintf('Toolbox "%s" does not support service "%s".', get_class($this), $serviceInterfaceName)
        );
    }

    /**
     * Внедряет в объект сервисы, известные набору инструментов.
     * @param object $object
     * @codeCoverageIgnore
     */
    public function injectDependencies($object)
    {
    }

    /**
     * Возвращает экземпляр фабрики.
     * Фабрика всегда имеет только один экземпляр.
     * @param string $factoryName имя фабрики
     * @param mixed[] $args аргументы конструктора для создания фабрики
     * @throws NotRegisteredException если фабрика не зарегистрирована
     * @throws RuntimeException если не удалось создать фабрику
     * @return mixed|IFactory экземпляр фабрики
     */
    protected function getFactory($factoryName, array $args = [])
    {
        if (isset($this->_factoryInstances[$factoryName])) {
            return $this->_factoryInstances[$factoryName];
        }

        if (!$this->hasFactory($factoryName)) {
            throw new NotRegisteredException($this->translate(
                'Factory "{name}" is not registered in toolbox "{toolbox}".',
                ['name' => $factoryName, 'toolbox' => get_class($this)]
            ));
        }
        list ($factoryClassName, $contracts) = $this->_registeredFactories[$factoryName];

        $options = $this->getFactoryConfig($factoryName);
        try {
            /**
             * @var IToolkitAware|IPrototype $prototype
             */
            $prototype = $this->getPrototypeFactory()
                ->create($factoryClassName, $contracts);

            $factory = $prototype->getPrototypeInstance();
            $this->_factoryInstances[$factoryName] = $factory;

            if ($factory instanceof IFactory) {
                $factory->setPrototypeFactory($this->getPrototypeFactory());
                $factory->setToolkit($this->getToolkit());
            }

            if ($options) {
                $prototype->setOptions($factory, $options);
            }

            $prototype->resolveDependencies();
            $prototype->invokeConstructor($factory, $args);

            return $factory;
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot create factory "{name}" for toolbox "{toolbox}".',
                ['name' => $factoryName, 'toolbox' => get_class($this)]
            ), 0, $e);
        }
    }

    /**
     * Проверяет, зарегистрирована ли фабрика с указанными именем
     * @param string $factoryName имя фабрики
     * @return boolean
     */
    protected function hasFactory($factoryName)
    {
        return isset($this->_registeredFactories[$factoryName]);
    }

    /**
     * Возвращает конфигурацию фабрики
     * @param string $factoryName имя фабрики
     * @throws UnexpectedValueException
     * @return array
     */
    private function getFactoryConfig($factoryName)
    {
        $this->factories = $this->configToArray($this->factories);
        if (array_key_exists($factoryName, $this->factories)) {
            return $this->configToArray($this->factories[$factoryName]);
        }

        return [];
    }

    /**
     * Регистрирует фабрику.
     * Фабрика всегда имеет только один экземпляр, который можно получить через getFactory().
     * @param string $factoryName имя фабрики
     * @param string $factoryClass имя класса фабрики по умолчанию
     * @param string[] $contracts список интерфейсов, которые должна реализовывать фабрика
     * @throws AlreadyRegisteredException если фабрика уже зарегистрирована
     * @return $this
     */
    protected function registerFactory($factoryName, $factoryClass, array $contracts = [])
    {
        if ($this->hasFactory($factoryName)) {
            throw new AlreadyRegisteredException($this->translate(
                'Factory "{factory}" already registered in "{toolbox}".',
                ['factory' => $factoryName, 'toolbox' => get_class($this)]
            ));
        }
        $this->_registeredFactories[$factoryName] = [$factoryClass, $contracts];

        return $this;
    }
}
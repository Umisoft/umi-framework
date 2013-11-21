<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\factory;

use umi\i18n\TLocalizable;
use umi\log\TLoggerAware;
use umi\spl\config\TConfigSupport;
use umi\toolkit\exception\AlreadyRegisteredException;
use umi\toolkit\exception\NotRegisteredException;
use umi\toolkit\exception\RequiredDependencyException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\exception\UnexpectedValueException;
use umi\toolkit\prototype\IPrototypeFactory;
use umi\toolkit\prototype\TPrototypeAware;
use umi\toolkit\TToolkitAware;

/**
 * Трейт для поддержки фабрики объектов.
 */
trait TFactory
{
    use TToolkitAware;
    use TLoggerAware;
    use TLocalizable;
    use TPrototypeAware;
    use TConfigSupport;

    /**
     * @var IPrototypeFactory $_prototypeFactory фабрика прототипов
     */
    private $_prototypeFactory;
    /**
     * @var array $_registeredFactories список зарегистрированных фабрик
     */
    private $_registeredFactories = [];

    /**
     * Устанавливает фабрику для создания прототипов
     * @param IPrototypeFactory $prototypeFactory
     * @return self
     */
    public function setPrototypeFactory(IPrototypeFactory $prototypeFactory)
    {
        $this->_prototypeFactory = $prototypeFactory;
        return $this;
    }

    /**
     * Возвращает фабрику прототипов сервисов.
     * @throws RequiredDependencyException если фабрика не была внедрена
     * @return IPrototypeFactory
     */
    protected function getPrototypeFactory()
    {
        if (!$this->_prototypeFactory) {
            throw new RequiredDependencyException(sprintf(
                'Prototype factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_prototypeFactory;
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

    /**
     * Возвращает экземпляр фабрики.
     * Фабрика всегда имеет только один экземпляр.
     * @param string $factoryName имя фабрики
     * @param mixed[] $args аргументы конструктора для создания фабрики
     * @throws RuntimeException если не удалось создать фабрику
     * @return IFactory экземпляр фабрики
     */
    protected function getFactory($factoryName, array $args = [])
    {
        list($factoryClassName, $contracts, $options) = $this->getFactoryInfo($factoryName);

        try {
            $prototype = $this->getPrototype($factoryClassName, $contracts);
            $factory = $prototype->createSingleInstance($args, $options);
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot create factory "{name}" for toolbox "{toolbox}".',
                ['name' => $factoryName, 'toolbox' => get_class($this)]
            ), 0, $e);
        }

        return $factory;
    }

    /**
     * Создает и возвращает новый экземпляр фабрики.
     * @param string $factoryName имя фабрики
     * @param mixed[] $args аргументы конструктора для создания фабрики
     * @throws RuntimeException если не удалось создать фабрику
     * @return IFactory экземпляр фабрики
     */
    protected function createFactory($factoryName, array $args = [])
    {
        list($factoryClassName, $contracts, $options) = $this->getFactoryInfo($factoryName);

        try {
            $prototype = $this->getPrototype($factoryClassName, $contracts);
            $factory = $prototype->createInstance($args, $options);
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot create factory "{name}" for toolbox "{toolbox}".',
                ['name' => $factoryName, 'toolbox' => get_class($this)]
            ), 0, $e);
        }

        return $factory;
    }

    /**
     * Возвращает информацию о фабрике (имя класса, контракты и значения публичных свойств)
     * @param string $factoryName имя фабрики
     * @throws NotRegisteredException если фабрика не зарегистрирована
     * @return array
     */
    private function getFactoryInfo($factoryName)
    {
        if (!$this->hasFactory($factoryName)) {
            throw new NotRegisteredException($this->translate(
                'Factory "{name}" is not registered in toolbox "{toolbox}".',
                ['name' => $factoryName, 'toolbox' => get_class($this)]
            ));
        }

        list ($factoryClassName, $contracts) = $this->_registeredFactories[$factoryName];

        $options = $this->getFactoryConfig($factoryName);

        return [$factoryClassName, $contracts, $options];
    }

    /**
     * Возвращает конфигурацию фабрики
     * @param string $factoryName имя фабрики
     * @throws UnexpectedValueException
     * @return array
     */
    private function getFactoryConfig($factoryName)
    {
        if (isset($this->factories)) {
            $this->factories = $this->configToArray($this->factories);
            if (array_key_exists($factoryName, $this->factories)) {
                return $this->configToArray($this->factories[$factoryName]);
            }
        }

        return [];
    }
}
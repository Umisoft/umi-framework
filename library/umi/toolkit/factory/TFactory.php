<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\factory;

use Traversable;
use umi\event\toolbox\TEventObservant;
use umi\i18n\TLocalizable;
use umi\log\TLoggerAware;
use umi\toolkit\exception\DomainException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\prototype\IPrototype;
use umi\toolkit\prototype\TPrototypeAware;
use umi\toolkit\TToolkitAware;

/**
 * Трейт для поддержки фабрики объектов.
 */
trait TFactory
{
    use TToolkitAware;
    use TPrototypeAware;
    use TLoggerAware;
    use TLocalizable;
    use TEventObservant;

    /**
     * @var object[] $_prototypes протитипы для создания экземпляров
     */
    private $_prototypes = [];
    /**
     * @var object[] $_instances единичные экземпляры, созданные через createSingleInstance()
     */
    private $_instances = [];

    /**
     * Возвращает единственный экземпляр указанного класса, null если экземпляр не существует
     * @param string $className имя класса
     * @return mixed|null
     */
    protected function getSingleInstance($className)
    {
        if (isset($this->_instances[$className])) {
            return $this->_instances[$className];
        }

        return null;
    }

    /**
     * Создает единственный экземпляр указанного класса и внедряет в него сервисы.
     * Если использующему классу необходимо производить какие-то действия над только что созданным экземпляром,
     * можно проверять его наличие через $this->getSingleInstance($className).
     * {@deprecated}
     * @param string $className имя класса
     * @param mixed[] $constructorArgs аргументы конструктора для создания
     * @param string[] $contracts список интерфейсов, которые должен реализовать экземпляр
     * @param array|Traversable $options опции, которые будут внедрены в существующие публичные свойства экземпляра
     * @throws RuntimeException если не существует класса, либо контракта
     * @throws DomainException если экземпляр класса не соответсвует контракту
     * @return mixed экземпляр класса
     */
    protected function createSingleInstance(
        $className,
        array $constructorArgs = [],
        array $contracts = [],
        array $options = []
    )
    {
        if (!isset($this->_instances[$className])) {
            $prototype = $this->getPrototype($className, $contracts);
            $object = $prototype->getPrototypeInstance();

            $this->_instances[$className] = $object;

            $prototype->invokeConstructor($object, $constructorArgs);

            if ($options) {
                $prototype->setOptions($object, $options);
            }
        }

        return $this->_instances[$className];
    }

    /**
     * Создает экземпляр указанного класса и внедряет в него сервисы, для повторного создания экземпляров будет использован прототип
     * {@deprecated}
     * @param string $className имя класса
     * @param mixed[] $constructorArgs аргументы конструктора для создания
     * @param string[] $contracts список интерфейсов, которые должен реализовать экземпляр
     * @param array|Traversable $options опции, которые будут внедрены в существующие публичные свойства экземпляра
     * @throws RuntimeException если не существует класса, либо контракта
     * @throws DomainException если экземпляр класса не соответсвует контракту
     * @return mixed экземпляр класса
     */
    protected function createInstance(
        $className,
        array $constructorArgs = [],
        array $contracts = [],
        array $options = []
    )
    {
        $prototype = $this->getPrototype($className, $contracts);
        $object = $prototype->createInstance($constructorArgs);

        if ($options) {
            $prototype->setOptions($object, $options);
        }

        return $object;
    }

    /**
     * Возвращает прототип сервиса.
     * @param string $className имя класса сервиса
     * @param array $contracts список контрактов, которые должен реализовывать сервис
     * @throws RuntimeException если не существует класса, либо контракта
     * @throws DomainException если прототип не соответствует какому-либо контракту
     * @return IPrototype
     */
    protected function getPrototype($className, array $contracts = [])
    {
        if (!isset($this->_prototypes[$className])) {
            $prototype = $this->getPrototypeFactory()
                ->create($className, $contracts);
            $this->initPrototype($prototype);

            $this->_prototypes[$className] = $prototype;

            $prototypeInstance = $prototype->getPrototypeInstance();
            if ($prototypeInstance instanceof IFactory) {
                $prototypeInstance->setPrototypeFactory($this->getPrototypeFactory());
                $prototypeInstance->setToolkit($this->getToolkit());
            }
            $prototype->resolveDependencies();

            $this->initPrototypeInstance($prototypeInstance);

        }

        return $this->_prototypes[$className];
    }

    /**
     * Инициализирует экземпляр прототипа.
     * Фабрика может внедрить в прототип известные ей внутренние зависимости.
     * @param object $prototypeInstance экземпляр прототипа
     */
    protected function initPrototypeInstance($prototypeInstance)
    {
    }

    /**
     * Инициализирует прототип.
     * Фабрика может внедрить в прототип известные ей внутренние зависимости.
     * @param IPrototype $prototype прототип
     */
    protected function initPrototype(IPrototype $prototype)
    {
    }

    /**
     * Восстанавливает зависимости указанного объекта
     * {@deprecated}
     * @param object $object объект
     * @param array $constructorArgs аргументы конструктора для восстанавления
     * @param array $contracts список интерфейсов, которые должен реализовать экземпляр
     * @param array|Traversable $options опции, которые будут внедрены в существующие публичные свойства экземпляра
     * @throws RuntimeException если не существует класса, либо контракта
     * @throws DomainException если экземпляр класса не соответсвует контракту
     * @return object экземпляр класса
     */
    protected function wakeUpInstance($object, array $constructorArgs = [], array $contracts = [], $options = [])
    {
        $className = get_class($object);
        $prototype = $this->getPrototype($className, $contracts);
        $prototype->wakeUpInstance($object);

        $this->initPrototypeInstance($object);

        if ($options) {
            $prototype->setOptions($object, $options);
        }

        return $object;
    }
}
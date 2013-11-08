<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\prototype;

use umi\toolkit\exception\DomainException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\IToolkitAware;
use UnexpectedValueException;

/**
 * Прототип объекта
 */
interface IPrototype extends IToolkitAware
{
    /**
     * Устанавливает контракты, которым должны следовать объекты, созданные на основе прототипа
     * @param array $contracts
     * @return self
     */
    public function setContracts(array $contracts);

    /**
     * Внедряет в прототип зависимости.
     * @return self
     */
    public function resolveDependencies();

    /**
     * Возвращает имя класса прототипа.
     * @return string
     */
    public function getClassName();

    /**
     * Создает и возвращает экземпляр объекта на основе прототипа.
     * @param array $constructorArgs аргументы конструктора
     * @return object
     */
    public function createInstance(array $constructorArgs = []);

    /**
     * Регистрирует билдер для зависимости, принимаемой в конструкторе
     * @param string $contract контракт
     * @param callable $builder билдер, разрешающий зависимость
     * @return self
     */
    public function registerConstructorDependency($contract, callable $builder);

    /**
     * Вызывает конструктор у экземпляра объекта, внедряет в конструктор известные сервисы.
     * @param object $object
     * @param array $constructorArgs аргументы конструктора
     * @return self
     */
    public function invokeConstructor($object, array $constructorArgs = []);

    /**
     * Устанавливает опции экземпляру объекта.
     * Опции будут внедрены в публичные свойства.
     * @param $object
     * @param array $options
     * @throws UnexpectedValueException если какая-либо из опций не соответствует заданному типу по умолчанию.
     * @return self
     */
    public function setOptions($object, array $options);

    /**
     * Восстанавливает зависимости объекта.
     * @param object $object
     * @return self
     */
    public function wakeUpInstance($object);

    /**
     * Возвращает экземпляр прототипа
     * @return object
     */
    public function getPrototypeInstance();

    /**
     * Проверяет объект на следование контрактам прототипа.
     * @param object $object
     * @throws DomainException если не соответсвует хотя бы одному контракту
     * @throws RuntimeException если контракт не существует
     * @return self
     */
    public function checkContracts($object);

}

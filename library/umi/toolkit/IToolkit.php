<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit;

use Traversable;
use umi\toolkit\exception\AlreadyRegisteredException;
use umi\toolkit\exception\InvalidArgumentException;
use umi\toolkit\prototype\IPrototype;

/**
 * Тулкит.
 */
interface IToolkit
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'toolkit';

    /**
     * Регистрирует набор инструментов
     * @param array|Traversable $toolboxConfig конфигурация набора инструментов
     * @throws InvalidArgumentException если нет обязательного аргумента конфигурации
     * @throws AlreadyRegisteredException если набор инструментов либо какой-либо из обслуживаемых сервисов был зарегистрирован ранее
     * @return self
     */
    public function registerToolbox($toolboxConfig);

    /**
     * Проверяет, зарегистрирован ли набор инструментов
     * @param string $toolboxName имя набора нструментов
     * @return bool
     */
    public function hasToolbox($toolboxName);

    /**
     * Регистрирует несколько наборов инструментов, используя конфигурацию
     * @param array|Traversable $config конфигурации наборов инструментов
     * @throws AlreadyRegisteredException если какой-либо набор инструментов уже зарегистрирован
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return self
     */
    public function registerToolboxes($config);

    /**
     * Регистрирует сервис.
     * Каждый раз при обращении к сервису через IToolkit::getService(), будет
     * вызван билдер для получения экземпляра сервиса.
     * Пример:
     * <code>
     *  $toolkit->registerService('umi\mail\IMail', function($concreteClassName, IToolkit $toolkit) {
     *      if ($concreteClassName) {
     *          return new $concreteClassName();
     *      } else {
     *          return new MyDefaultMail();
     *      }
     *  }
     * </code>
     * @param string $serviceInterfaceName интерфейс сервиса
     * @param callable $builder билдер сервиса, создающий его экземпляр.
     * @throws AlreadyRegisteredException если сервис с указанным интерфейсом был зарегистрирован ранее
     * @return self
     */
    public function registerService($serviceInterfaceName, callable $builder);

    /**
     * Проверяет, зарегистрирован ли сервис
     * @param string $serviceInterfaceName имя интерфейса сервиса
     * @return bool
     */
    public function hasService($serviceInterfaceName);

    /**
     * Возвращает экземпляр сервиса.
     * @param string $serviceInterfaceName имя интерфейса сервиса
     * @param null|string $concreteClassName класс конкретной реализации сервиса, может быть учтен при
     * получении экземпляра сервиса.
     * @return object
     */
    public function getService($serviceInterfaceName, $concreteClassName = null);

    /**
     * Возвращает билдер сервиса, который подходит под первый из указанных контрактов
     * @param array $contracts список контрактов
     * @return null|callable билдер сервиса, либо null если билдер не найден
     */
    public function findServiceBuilderByContracts(array $contracts);

    /**
     * Регистрирует инжектор для указанного интерфейса.
     * Инжектор может внедрять известные ему зависимости в объект.
     * Пример:
     * <code>
     *  $toolkit->registerAwareInterface('umi\logger\ILoggerAware', function($object, IToolkit $toolkit) {
     *      if ($object instanceof umi\logger\ILoggerAware) {
     *          $loggerService = $toolkit->get('Psr\Log\LoggerInterface');
     *          $object->setLogger($loggerService);
     *      }
     *  }
     * </code>
     * @param string $awareInterfaceName имя обслуживаемого интерфейса
     * @param callable $injector
     * @throws AlreadyRegisteredException если инжектор для указанного интерфейса был зарегистрирован ранее
     * @return self
     */
    public function registerAwareInterface($awareInterfaceName, callable $injector);

    /**
     * Проверяет, зарегистрирован ли инжектор для указанного интерфейса.
     * @param string $awareInterfaceName имя обслуживаемого интерфейса
     * @return bool
     */
    public function hasAwareInterface($awareInterfaceName);

    /**
     * Возвращает список инжекторов, которые могут обслужить объект,
     * имплементирующий указанный набор интерфейсов.
     * @param array $interfaceNames список имен интерфейсов
     * @return callable[] список инжекторов
     */
    public function getInjectors(array $interfaceNames);

    /**
     * Устанавливает настройки тулкита.
     * @param array|Traversable $settings конфигурация в формате ['toolboxName' => [конфигурация], ...]
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return self
     */
    public function setSettings($settings);

    /**
     * Возвращает прототип класса.
     * @param string $className имя класса
     * @param array $contracts список контрактов, которые должен реализовывать экземпляр класса
     * @return IPrototype
     */
    public function getPrototype($className, array $contracts = []);

}

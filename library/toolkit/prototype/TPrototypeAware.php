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
use umi\toolkit\factory\IFactory;
use umi\toolkit\IToolkit;

/**
 * Трейт для поддержки работы с прототипами
 */
trait TPrototypeAware
{
    /**
     * @var object[] $_prototypes протитипы для создания экземпляров
     */
    private $_prototypes = [];

    /**
     * Возвращает фабрику прототипов сервисов.
     * @return IPrototypeFactory
     */
    abstract protected function getPrototypeFactory();

    /**
     * Возвращает toolkit.
     * @return IToolkit
     */
    abstract protected function getToolkit();

    /**
     * Возвращает прототип класса.
     * @param string $className имя класса
     * @param array $contracts список контрактов, которые должен реализовывать экземпляр класса
     * @param callable $prototypeInitializer инициализатор, который будет вызван после создания прототипа
     * @throws RuntimeException если не существует класса, либо контракта
     * @throws DomainException если прототип не соответствует какому-либо контракту
     * @return IPrototype
     */
    protected function getPrototype($className, array $contracts = [], callable $prototypeInitializer = null)
    {
        if (!isset($this->_prototypes[$className])) {
            $prototype = $this->getPrototypeFactory()
                ->create($className, $contracts);

            if (is_callable($prototypeInitializer)) {
                call_user_func_array($prototypeInitializer, [$prototype]);
            }

            $this->_prototypes[$className] = $prototype;

            $prototypeInstance = $prototype->getPrototypeInstance();
            if ($prototypeInstance instanceof IFactory) {
                $prototypeInstance->setPrototypeFactory($this->getPrototypeFactory());
                $prototypeInstance->setToolkit($this->getToolkit());
            }
            $prototype->resolveDependencies();
        }

        return $this->_prototypes[$className];
    }
}
 
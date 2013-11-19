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
use umi\toolkit\exception\DomainException;
use umi\toolkit\exception\RequiredDependencyException;
use umi\toolkit\exception\RuntimeException;
use umi\toolkit\prototype\IPrototype;
use umi\toolkit\prototype\IPrototypeFactory;
use umi\toolkit\TToolkitAware;

/**
 * Трейт для поддержки фабрики объектов.
 */
trait TFactory
{
    use TToolkitAware;
    use TLoggerAware;
    use TLocalizable;

    /**
     * @var object[] $_prototypes протитипы для создания экземпляров
     */
    private $_prototypes = [];
    /**
     * @var object[] $_instances единичные экземпляры, созданные через createSingleInstance()
     */
    private $_instances = [];

    /**
     * @var IPrototypeFactory $_prototypeFactory
     */
    private $_prototypeFactory;

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
     * Возвращает прототип класса.
     * @param string $className имя класса
     * @param array $contracts список контрактов, которые должен реализовывать экземпляр класса
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


}
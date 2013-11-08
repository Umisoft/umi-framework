<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\prototype;

use umi\toolkit\exception\RequiredDependencyException;

/**
 * Трейт для поддержки создания прототипов.
 */
trait TPrototypeAware
{
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
                'Prototype factory are not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_prototypeFactory;
    }
}

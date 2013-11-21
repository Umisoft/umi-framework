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
use umi\toolkit\exception\RequiredDependencyException;
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

}
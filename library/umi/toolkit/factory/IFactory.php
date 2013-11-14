<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\factory;

use umi\i18n\ILocalizable;
use umi\log\ILoggerAware;
use umi\toolkit\IToolkit;
use umi\toolkit\prototype\IPrototypeFactory;

/**
 * Фабрика для создания объектов.
 */
interface IFactory extends ILoggerAware, ILocalizable
{
    /**
     * Устанавливает toolkit.
     * @param IToolkit $toolkit
     */
    public function setToolkit(IToolkit $toolkit);

    /**
     * Устанавливает фабрику для создания прототипов
     * @param IPrototypeFactory $prototypeFactory
     * @return self
     */
    public function setPrototypeFactory(IPrototypeFactory $prototypeFactory);
}

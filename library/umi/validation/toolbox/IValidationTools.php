<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\toolbox;

use umi\toolkit\toolbox\IToolbox;
use umi\validation\IValidatorFactory;

/**
 * Набор инструментов валидации.
 */
interface IValidationTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'validation';

    /**
     * Возвращает фабрику для создания валидаторов.
     * @return IValidatorFactory
     */
    public function getValidatorFactory();
}
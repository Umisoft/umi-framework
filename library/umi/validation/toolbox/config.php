<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'toolboxInterface'    => 'umi\validation\toolbox\IValidationTools',
    'defaultClass'        => 'umi\validation\toolbox\ValidationTools',
    'servicingInterfaces' => [
        'umi\validation\IValidationAware'
    ],
    'aliases'             => [IValidationTools::ALIAS]
];

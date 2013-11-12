<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'name'    => PaginationTools::NAME,
    'class'        => __NAMESPACE__ . '\PaginationTools',
    'servicingInterfaces' => [
        'umi\pagination\IPaginationAware'
    ]
];
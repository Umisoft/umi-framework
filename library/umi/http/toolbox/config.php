<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'toolboxInterface'    => __NAMESPACE__ . '\IHttpTools',
    'defaultClass'        => __NAMESPACE__ . '\HttpTools',
    'servicingInterfaces' => [
        'umi\http\IHttpFactory',
        'umi\http\IHttpAware',
        'umi\http\request\param\IParamCollectionAware',
        'umi\http\response\header\IHeaderCollectionAware',
    ],
    'services'            => [
        'umi\http\request\IRequest',
        'umi\http\response\IResponse',
    ],
    'aliases'             => [IHttpTools::ALIAS]
];

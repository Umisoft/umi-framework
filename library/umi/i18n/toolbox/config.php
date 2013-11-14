<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'name'    => I18nTools::NAME,
    'class'   => __NAMESPACE__ . '\I18nTools',
    'awareInterfaces' => [
        'umi\i18n\ILocalizable',
        'umi\i18n\ILocalesAware'
    ],
    'services'            => [
        'umi\i18n\ILocalesService',
        'umi\i18n\translator\ITranslator'
    ]
];

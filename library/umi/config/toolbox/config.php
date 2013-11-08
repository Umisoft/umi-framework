<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'toolboxInterface'    => 'umi\config\toolbox\IConfigTools',
    'defaultClass'        => 'umi\config\toolbox\ConfigTools',
    'servicingInterfaces' => [
        'umi\config\entity\factory\IConfigEntityFactoryAware',
        'umi\config\io\IConfigIOAware',
        'umi\config\io\IConfigAliasResolverAware',
        'umi\config\io\IConfigCacheEngineAware',
    ],
    'services'            => [
        'umi\config\io\IConfigIO'
    ],
    'aliases'             => [IConfigTools::ALIAS]
];
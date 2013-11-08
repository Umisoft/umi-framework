<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\fixture\component1\component\component2;

use umi\route\type\factory\IRouteFactory;
use umi\templating\engine\ITemplateEngineFactory;

return [
    'controllers' => [
        'test' => __NAMESPACE__ . '\controller\TestController',
    ],
    'view'        => [
        'type'      => ITemplateEngineFactory::PHP_ENGINE,
        'directory' => __DIR__ . '/view',
        'extension' => 'phtml'
    ],
    'routes'      => [
        'test' => [
            'type'     => IRouteFactory::ROUTE_FIXED,
            'route'    => '/test',
            'defaults' => [
                'controller' => 'test',
                'route'      => 'example'
            ]
        ],
    ],
];
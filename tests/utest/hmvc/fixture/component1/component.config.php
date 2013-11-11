<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\fixture\component1;

use umi\route\IRouteFactory;
use umi\templating\engine\ITemplateEngineFactory;

return [
    'components'  => [
        'component2' => require __DIR__ . '/component/component2/component.config.php'
    ],
    'controllers' => [
        'response' => __NAMESPACE__ . '\controller\ResponseController',
        'result'   => __NAMESPACE__ . '\controller\ResultController'
    ],
    'view'        => [
        'type'      => ITemplateEngineFactory::PHP_ENGINE,
        'directory' => __DIR__ . '/view',
        'extension' => 'phtml'
    ],
    'routes'      => [
        'response'   => [
            'type'     => IRouteFactory::ROUTE_FIXED,
            'route'    => '/response',
            'defaults' => [
                'controller' => 'response',
                'route'      => 'example'
            ]
        ],
        'result'     => [
            'type'     => IRouteFactory::ROUTE_FIXED,
            'route'    => '/result',
            'defaults' => [
                'controller' => 'result',
                'route'      => 'example'
            ]
        ],
        'component2' => [
            'type'     => IRouteFactory::ROUTE_FIXED,
            'route'    => '/component2',
            'defaults' => [
                'component' => 'component2',
            ]
        ]
    ],
];
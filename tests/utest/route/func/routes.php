<?php
return [
    'application' => [
        'type'      => 'fixed',
        'route'     => '/application',
        'defaults'  => [
            'application' => 'public',
            'module'      => 'base',
            'method'      => 'index'
        ],
        'subroutes' => [
            'default' => [
                'type'  => 'simple',
                'route' => '/{module:string}/{method:string}'
            ],
            'login'   => [
                'type'     => 'fixed',
                'route'    => '/login',
                'defaults' => [
                    'module' => 'user',
                    'method' => 'login'
                ]
            ],
            'test'    => [
                'type'     => 'fixed',
                'route'    => '/test',
                'defaults' => [
                    'p' => 'test'
                ]
            ]
        ]
    ],
    'admin'       => [
        'type'      => 'fixed',
        'route'     => '/admin',
        'defaults'  => [
            'application' => 'backend',
            'module'      => 'user',
            'method'      => 'login'
        ],
        'subroutes' => [
            'default' => [
                'type'  => 'simple',
                'route' => '/{module:string}/{method:string}'
            ],
            'edit'    => [
                'type'  => 'simple',
                'route' => '/{module:string}/{element:integer}'
            ]
        ]
    ],
];
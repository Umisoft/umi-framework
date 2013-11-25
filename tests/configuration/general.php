<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\tests\configuration\master;

use umi\dbal\toolbox\DbalTools;
use umi\orm\collection\ICollectionFactory;
use umi\orm\toolbox\ORMTools;

/**
 * Конфигурация для тестирования, используемая по умолчанию.
 * Для настройки конкретного тестового окружения необходимо переопределить необходимые
 * опции в локальной конфигурации.
 */
return [
    /**
     * Идентификатор мастер-сервера БД для тестирования по умолчанию
     */
    'defaultServer' => 'sqliteMaster',
    /**
     * Идентификатор мастер-сервера для тестов, использующих sqlite
     */
    'sqliteServer'  => 'sqliteMaster',
    /**
     * Идентификатор мастер-сервера для для тестов, использующих mysql
     */
    'mysqlServer'   => 'mysqlMaster',
    /**
     * Директории
     */
    'directory'     => [
        'ormMetadata' => 'collections'
    ],
    /**
     * Конфигурация тулкита для тестового окружения
     */
    'settings'      => [
        DbalTools::NAME           => [
            'servers' => [
                [
                    'id'     => 'mysqlMaster',
                    'type'   => 'master',
                    'driver' => [
                        'type'    => 'mysql',
                        'options' => [
                            'dsn'      => 'mysql:dbname=umi;host=localhost',
                            'user'     => 'root',
                            'password' => 'root'
                        ]
                    ]
                ],
                [
                    'id'     => 'sqliteMaster',
                    'type'   => 'master',
                    'driver' => [
                        'type'    => 'sqlite',
                        'options' => [
                            'dsn' => 'sqlite::memory:'
                        ]
                    ]
                ]
            ]
        ],
        ORMTools::NAME            => [
            'metadata' => [
                'system_hierarchy'       => '{#lazy:~/collections/system/system_hierarchy.config.php}',
                'system_menu'            => '{#lazy:~/collections/system/system_menu.config.php}',
                'guides_country'         => '{#lazy:~/collections/guides/guides_country.config.php}',
                'guides_city'            => '{#lazy:~/collections/guides/guides_city.config.php}',
                'users_user'             => '{#lazy:~/collections/users/users_user.config.php}',
                'users_profile'          => '{#lazy:~/collections/users/users_profile.config.php}',
                'users_group'            => '{#lazy:~/collections/users/users_group.config.php}',
                'blogs_blog'             => '{#lazy:~/collections/blogs/blogs_blog.config.php}',
                'blogs_post'             => '{#lazy:~/collections/blogs/blogs_post.config.php}',
                'blogs_blog_subscribers' => '{#lazy:~/collections/blogs/blogs_blog_subscribers.config.php}'
            ],
            'collections' => [
                'system_hierarchy'       => [
                    'type' => ICollectionFactory::TYPE_COMMON_HIERARCHY
                ],
                'system_menu'            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC
                ],
                'guides_country'         => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                'guides_city'            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                'users_user'             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                'users_profile'          => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                'users_group'            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                'blogs_blog'             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'class'     => 'utest\orm\mock\collections\blogs\BlogsCollection',
                    'hierarchy' => 'system_hierarchy'
                ],
                'blogs_post'             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'hierarchy' => 'system_hierarchy'
                ],
                'blogs_blog_subscribers' => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ]
        ]
    ]
];
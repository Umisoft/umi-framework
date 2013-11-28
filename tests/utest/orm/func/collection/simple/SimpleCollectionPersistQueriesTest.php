<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\simple;

use utest\orm\ORMDbTestCase;

/**
 * Тесты запросов коммита объектов простой коллекции
 */
class SimpleCollectionPersistQueriesTest extends ORMDbTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::SYSTEM_HIERARCHY,
            self::GUIDES_COUNTRY,
            self::GUIDES_CITY,
            self::USERS_GROUP,
            self::USERS_USER,
            self::USERS_PROFILE,
        ];
    }

    public function testAddModifyDeleteObjectQueries()
    {

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $user = $userCollection->add();
        $user->setValue('login', 'test_login');

        $this->objectPersister->commit();
        $queries = [
            ['start', []],
            [
                'insert',
                [
                    ':type'  => 'users_user.base',
                    ':guid'  => $user->getGUID(),
                    ':login' => 'test_login',
                ]
            ],
            ['commit', []],
        ];

        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'После добавления объекта ожидается один INSERT'
        );

        $this->resetQueries();
        $this->objectPersister->commit();
        $this->assertEquals(
            [],
            $this->getQueries(),
            'Ожидается, что после повторного коммита без изменений не будет произведено ни одного запроса'
        );

        $this->resetQueries();
        $user->setValue('login', 'new_test_login');
        $this->objectPersister->commit();

        $queries = [
            ['start', []],
            [
                'update',
                [
                    ':login'      => 'new_test_login',
                    ':objectId'   => 1,
                    ':version'    => 1,
                ]
            ],
            ['commit', []],
        ];

        $queryTypesWithParams = $this->getQueryTypesWithParams();


        $this->assertEquals(
            $queries,
            $queryTypesWithParams,
            'После изменения объекта ожидается один UPDATE-запрос'
        );
        $this->assertEquals(
            'UPDATE "umi_mock_users"
SET "login" = :login, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            $this->getOnlyQueries('update')[0],
            'Запрос должен содержать инкремент версии'
        );

        $this->resetQueries();
        $this->objectPersister->commit();
        $this->assertEquals(
            [],
            $this->getQueries(),
            'Ожидается, что после повторного коммита без изменений не будет произведено ни одного запроса'
        );

        $this->resetQueries();
        $this->objectPersister->markAsDeleted($user);
        $this->objectPersister->commit();
        $queries = [
            ['start', []],
            [
                'delete',
                [
                    ':objectId' => 1
                ]
            ],
            ['commit', []],
        ];
        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'После удаления объекта ожидается один DELETE-запрос'
        );

        $this->resetQueries();
        $this->objectPersister->commit();
        $this->assertEquals(
            [],
            $this->getQueries(),
            'Ожидается, что после повторного коммита без изменений не будет произведено ни одного запроса'
        );

    }

    public function testBelongsToRelationQueries()
    {

        $groupCollection = $this->collectionManager->getCollection(self::USERS_GROUP);

        $group = $groupCollection->add();
        $group->setValue('name', 'test_group1');

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $user = $userCollection->add();
        $user->setValue('login', 'test_login');
        $user->setValue('group', $group);

        $this->objectPersister->commit();

        $queries = [
            ['start', []],
            [
                'insert',
                [
                    ':type' => 'users_group.base',
                    ':guid' => $group->getGUID(),
                    ':name' => 'test_group1'
                ]
            ],
            [
                'insert',
                [
                    ':type'     => 'users_user.base',
                    ':guid'     => $user->getGUID(),
                    ':login'    => 'test_login',
                    ':group_id' => null
                ]
            ],
            [
                'update',
                [
                    ':group_id'   => 1,
                    ':objectId'   => 1,
                    ':version'    => 1
                ]
            ],
            ['commit', []],
        ];

        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'После добавления двух связанных объектов ожидаются два INSERT-запроса и 1 UPDATE-запрос'
        );

        $this->assertEquals(
            'UPDATE "umi_mock_users"
SET "group_id" = :group_id, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            $this->getOnlyQueries('update')[0],
            'После добавления двух связанных объектов ожидается UPDATE с инкрементом версий'
        );


        $this->assertEquals(2, $user->getVersion(), 'Ожидается, что у пользователя версия 2');

        $this->resetQueries();

        $group2 = $groupCollection->add();
        $group2->setValue('name', 'test_group2');
        $user->setValue('group', $group2);

        $this->objectPersister->commit();

        $queries = [
            ['start', []],
            [
                'insert',
                [
                    ':type' => 'users_group.base',
                    ':guid' => $group2->getGUID(),
                    ':name' => 'test_group2',
                ]
            ],
            [
                'update',
                [
                    ':group_id'   => 2,
                    ':objectId'   => 1,
                    ':version'    => 2
                ]
            ],
            ['commit', []],
        ];

        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'После добавления объекта и выставления его в качестве значения ожидается один INSERT-запрос '
            . 'и один UPDATE-запрос'
        );
        $this->assertEquals(3, $user->getVersion(), 'Ожидается, что у пользователя версия 3');

        $this->assertEquals(
            'UPDATE "umi_mock_users"
SET "group_id" = :group_id, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            $this->getOnlyQueries('update')[0],
            'После добавления двух связанных объектов ожидается UPDATE с инкрементом версии'
        );

        $this->resetQueries();
        $user->setValue('group', $group);
        $this->objectPersister->commit();

        $queries = [
            ['start', []],
            [
                'update',
                [
                    ':group_id'   => 1,
                    ':objectId'   => 1,
                    ':version'    => 3,
                ]
            ],
            ['commit', []],
        ];

        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'После установления связи между 2-мя существующими запросами ожидается один UPDATE-запрос'
        );
        $this->assertEquals(4, $user->getVersion(), 'Ожидается, что у пользователя версия 4');

        $this->resetQueries();
        $user->unload();
        $group->unload();

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $user = $userCollection->getById(1);
        $user->setValue('group', $group2);
        $this->objectPersister->commit();

        $queries = [
            [
                'select',
                [
                    ':value0' => 1
                ]
            ],
            [
                'select',
                [
                    ':value0' => 1
                ]
            ],
            ['start', []],
            [
                'update',
                [
                    ':group_id'   => 2,
                    ':objectId'   => 1,
                    ':version'    => 4
                ]
            ],
            ['commit', []],
        ];
        $this->assertEquals(
            $queries,
            $this->getQueryTypesWithParams(),
            'Ожидается два SELECT-запроса и один UPDATE запрос при выставления значения незагруженному объекту '
            . 'с существующим значением'
        );
        $this->assertEquals(
            'UPDATE "umi_mock_users"
SET "group_id" = :group_id, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            $this->getOnlyQueries('update')[0],
            'Ожидается инкремент версии при выставления значения незагруженному объекту с существующим значением'
        );
        $this->assertEquals(5, $user->getVersion(), 'Ожидается, что у пользователя версия 5');
    }
}

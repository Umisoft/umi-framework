<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\dbal\builder\IQueryBuilder;
use umi\dbal\cluster\IConnection;
use umi\event\IEvent;
use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ISimpleCollection;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

/**
 * Тесты запросов загрузки объекта
 */
class ObjectQueriesTest extends ORMDbTestCase
{

    public $queries = [];

    /**
     * @var ISimpleCollection $userCollection
     */
    protected $userCollection;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_GROUP            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {
        $this->userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $this->userCollection->add()
            ->setValue('login', '123')
            ->setValue('height', 123);

        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $this->getDbCluster()
            ->getDbDriver()
            ->bindEvent(
            IConnection::EVENT_AFTER_EXECUTE_QUERY,
            function (IEvent $event) {
                /**
                 * @var IQueryBuilder $builder
                 */
                $builder = $event->getParam('queryBuilder');
                if ($builder) {
                    $this->queries[] = $builder->getSql();
                }
            }
        );
    }

    public function testQueries()
    {

        $user = $this->userCollection->select()
            ->fields(['login'])
            ->where(IObject::FIELD_IDENTIFY)
            ->equals(1)
            ->result()
            ->fetch();

        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`login` AS `users_user:login`
FROM `umi_mock_users` AS `users_user`
WHERE ((`users_user`.`id` = :value0))'
            ],
            $this->queries,
            'Ожидается, что при получении объекта будут выбраны только обязательные и запрошенные свойства'
        );

        $this->queries = [];
        $this->assertEquals('123', $user->getValue('login'));
        $this->assertEmpty(
            $this->queries,
            'Ожидается, что для получения ранее загруженных свойств не будут выполнены запросы'
        );

        $this->assertEquals('123', $user->getValue('height'));
        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`
FROM `umi_mock_users` AS `users_user`
WHERE ((`users_user`.`id` = :value0))'
            ],
            $this->queries,
            'Ожидается, что при запросе незагруженного свойства все свойства будут догружены'
        );

        $this->queries = [];
        $user->reset();
        $this->assertEquals(1, $user->getId());
        $this->assertEmpty(
            $this->queries,
            'Ожидается, что после сброса значений свойств объекта останутся значения для id, GUID и type'
        );

        $this->assertEquals('123', $user->getValue('height'));
        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`login` AS `users_user:login`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`
FROM `umi_mock_users` AS `users_user`
WHERE ((`users_user`.`id` = :value0))'
            ],
            $this->queries,
            'Ожидается, что при запросе незагруженного свойства все свойства будут догружены'
        );

    }

}

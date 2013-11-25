<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\orm\collection\ISimpleCollection;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

/**
 * Тесты запросов загрузки объекта
 */
class ObjectQueriesTest extends ORMDbTestCase
{
    /**
     * @var ISimpleCollection $userCollection
     */
    protected $userCollection;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::USERS_GROUP,
            self::USERS_USER
        ];
    }

    protected function setUpFixtures()
    {
        $this->userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $this->userCollection->add()
            ->setValue('login', '123')
            ->setValue('height', 123);

        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();
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
            $this->getQueries(),
            'Ожидается, что при получении объекта будут выбраны только обязательные и запрошенные свойства'
        );

        $this->setQueries([]);
        $this->assertEquals('123', $user->getValue('login'));
        $this->assertEmpty(
            $this->getQueries(),
            'Ожидается, что для получения ранее загруженных свойств не будут выполнены запросы'
        );

        $this->assertEquals('123', $user->getValue('height'));
        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`
FROM `umi_mock_users` AS `users_user`
WHERE ((`users_user`.`id` = :value0))'
            ],
            $this->getQueries(),
            'Ожидается, что при запросе незагруженного свойства все свойства будут догружены'
        );

        $this->setQueries([]);
        $user->reset();
        $this->assertEquals(1, $user->getId());
        $this->assertEmpty(
            $this->getQueries(),
            'Ожидается, что после сброса значений свойств объекта останутся значения для id, GUID и type'
        );

        $this->assertEquals('123', $user->getValue('height'));
        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`login` AS `users_user:login`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`
FROM `umi_mock_users` AS `users_user`
WHERE ((`users_user`.`id` = :value0))'
            ],
            $this->getQueries(),
            'Ожидается, что при запросе незагруженного свойства все свойства будут догружены'
        );

    }
}

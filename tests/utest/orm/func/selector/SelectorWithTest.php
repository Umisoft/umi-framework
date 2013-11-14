<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\selector;

use umi\dbal\builder\IQueryBuilder;
use umi\dbal\cluster\IConnection;
use umi\event\IEvent;
use umi\orm\collection\ISimpleCollection;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

class SelectorWithTest extends ORMDbTestCase
{

    public $queries = [];
    /**
     * @var ISimpleCollection $userCollection
     */
    protected $userCollection;
    /**
     * @var ISimpleCollection $profileCollection
     */
    protected $profileCollection;
    /**
     * @var ISimpleCollection $countryCollection
     */
    protected $countryCollection;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::USERS_USER,
            self::USERS_GROUP,
            self::USERS_PROFILE,
            self::GUIDES_CITY,
            self::GUIDES_COUNTRY
        ];
    }

    protected function setUpFixtures()
    {

        $this->countryCollection = $this->collectionManager->getCollection(self::GUIDES_COUNTRY);

        $country = $this->countryCollection->add();
        $country->setValue('name', 'Россия');

        $cityCollection = $this->collectionManager->getCollection(self::GUIDES_CITY);

        $city = $cityCollection->add();
        $city->setValue('name', 'Санкт-Петербург');
        $city->setValue('country', $country);

        $groupCollection = $this->collectionManager->getCollection(self::USERS_GROUP);
        $group = $groupCollection->add();
        $group->setValue('name', 'Группа');

        $this->userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $user1 = $this->userCollection->add();
        $user1->setValue('login', 'test_login1');
        $user1->setValue('group', $group);

        $user2 = $this->userCollection->add();
        $user2->setValue('login', 'test_login2');

        $this->profileCollection = $this->collectionManager->getCollection(self::USERS_PROFILE);

        $profile = $this->profileCollection->add('natural_person');
        $profile->setValue('name', 'test_name1');
        $profile->setValue('user', $user1);
        $profile->setValue('city', $city);

        $this->objectPersister->commit();

        $this->queries = [];

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

    public function testWrongWithExceptions()
    {

        $selector = $this->userCollection->select();

        $e = null;
        try {
            $selector->with('nonExistentField');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выбрать объект вместе с несуществующей связью'
        );
        $this->assertEquals(
            'Cannot resolve field path "nonExistentField". Field "nonExistentField" does not exist in "users_user".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $selector->with('group.nonExistentField');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выбрать объект вместе cо связью, отличной от belongsTo и HasOne'
        );
        $this->assertEquals(
            'Cannot resolve field path "group.nonExistentField". Field "nonExistentField" does not exist in "users_group".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $selector->with('subscription');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выбрать объект вместе с relation полями не belongsTo и не HasOne'
        );
        $this->assertEquals(
            'Cannot select with related object. Cannot resolve field path "subscription". Field "subscription" is not "belongs-to" relation.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $selector->with('group.users');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выбрать объект вместе cо связью, отличной от belongsTo и HasOne'
        );
        $this->assertEquals(
            'Cannot select with related object. Cannot resolve field path "group.users". Field "users" is not "belongs-to" relation.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $selector->with('group', ['nonExistentField']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке выбрать объект вместе cо связью, отличной от belongsTo и HasOne'
        );
        $this->assertEquals(
            'Cannot select with related object. Field "nonExistentField" does not exist in collection "users_group".',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testWithBelongsTo()
    {
        $selector1 = $this->userCollection->select()
            ->where('login')
            ->equals('test_login1');
        $selector1->with('group');

        $sql = $selector1->getSelectBuilder()
            ->getSql();
        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`login` AS `users_user:login`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`, `users_user`.`supervisor_field` AS `users_user:supervisorField`, `users_user`.`guest_field` AS `users_user:guestField`, `users_user:group`.`id` AS `users_user:group:id`, `users_user:group`.`guid` AS `users_user:group:guid`, `users_user:group`.`type` AS `users_user:group:type`, `users_user:group`.`version` AS `users_user:group:version`, `users_user:group`.`name` AS `users_user:group:name`, `users_user:group`.`title` AS `users_user:group:title#ru-RU`, `users_user:group`.`title_en` AS `users_user:group:title#en-US`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_groups` AS `users_user:group` ON `users_user:group`.`id` = `users_user`.`group_id`
WHERE ((`users_user`.`login` = :value0))',
            $sql,
            'Неверный запрос на выборку со связной сущностью'
        );
        $user1 = $selector1->result()
            ->fetch();
        $this->queries = [];

        $this->assertInstanceOf('umi\orm\object\IObject', $user1->getValue('group'));
        $this->assertEmpty($this->queries);

        $selector2 = $this->userCollection->select()
            ->where('login')
            ->equals('test_login1');
        $selector2->with('group', ['name']);

        $sql = $selector2->getSelectBuilder()
            ->getSql();
        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`login` AS `users_user:login`, `users_user`.`email` AS `users_user:email`, `users_user`.`password` AS `users_user:password`, `users_user`.`is_active` AS `users_user:isActive`, `users_user`.`rating` AS `users_user:rating`, `users_user`.`height` AS `users_user:height`, `users_user`.`group_id` AS `users_user:group`, `users_user`.`supervisor_field` AS `users_user:supervisorField`, `users_user`.`guest_field` AS `users_user:guestField`, `users_user:group`.`id` AS `users_user:group:id`, `users_user:group`.`guid` AS `users_user:group:guid`, `users_user:group`.`type` AS `users_user:group:type`, `users_user:group`.`version` AS `users_user:group:version`, `users_user:group`.`name` AS `users_user:group:name`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_groups` AS `users_user:group` ON `users_user:group`.`id` = `users_user`.`group_id`
WHERE ((`users_user`.`login` = :value0))',
            $sql,
            'Неверный запрос на выборку со связной сущностью c указанными полями'
        );

        $selector3 = $this->userCollection->select()
            ->where('login')
            ->equals('test_login2');
        $selector3->with('group');

        $user2 = $selector3->result()
            ->fetch();
        $this->queries = [];

        $this->assertNull($user2->getValue('group'));

        $this->assertEmpty($this->queries);
    }

    public function testWithAndThrough()
    {

        $selector = $this->userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->with('group', ['name'])
            ->where('group.name')
            ->equals('Группа');

        $sql = $selector->getSelectBuilder()
            ->getSql();

        $this->assertEquals(
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`, `users_user`.`group_id` AS `users_user:group`, `users_user:group`.`id` AS `users_user:group:id`, `users_user:group`.`guid` AS `users_user:group:guid`, `users_user:group`.`type` AS `users_user:group:type`, `users_user:group`.`version` AS `users_user:group:version`, `users_user:group`.`name` AS `users_user:group:name`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_groups` AS `users_user:group` ON `users_user:group`.`id` = `users_user`.`group_id`
WHERE ((`users_user:group`.`name` = :value0))',
            $sql,
            'Неверный запрос на выборку со связной сущностью c указанными полями'
        );

        $user = $selector->result()
            ->fetch();
        $this->queries = [];

        $this->assertInstanceOf('umi\orm\object\IObject', $user->getValue('group'));
        $this->assertEmpty($this->queries);

    }

    public function testWithThrough()
    {

        $selector = $this->profileCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->with('city.country');

        $sql = $selector->getSelectBuilder()
            ->getSql();

        $this->assertEquals(
            'SELECT `users_profile`.`id` AS `users_profile:id`, `users_profile`.`guid` AS `users_profile:guid`, `users_profile`.`type` AS `users_profile:type`, `users_profile`.`version` AS `users_profile:version`, `users_profile`.`city_id` AS `users_profile:city`, `users_profile:city:country`.`id` AS `users_profile:city:country:id`, `users_profile:city:country`.`guid` AS `users_profile:city:country:guid`, `users_profile:city:country`.`type` AS `users_profile:city:country:type`, `users_profile:city:country`.`version` AS `users_profile:city:country:version`, `users_profile:city:country`.`name` AS `users_profile:city:country:name`
FROM `umi_mock_profiles` AS `users_profile`
	LEFT JOIN `umi_mock_cities` AS `users_profile:city` ON `users_profile:city`.`id` = `users_profile`.`city_id`
	LEFT JOIN `umi_mock_countries` AS `users_profile:city:country` ON `users_profile:city:country`.`id` = `users_profile:city`.`country_id`
WHERE 1',
            $sql,
            'Неверный запрос на выборку со связной сущностью c указанными полями'
        );

        $profile = $selector->result()
            ->fetch();
        $this->queries = [];

        $this->assertInstanceOf('umi\orm\object\IObject', $profile->getValue('city'));
        $this->assertEmpty($this->queries);

        $country = $this->countryCollection->getById(1);
        $this->assertInstanceOf('umi\orm\object\IObject', $country);
        $this->assertEmpty($this->queries);

    }

    public function testDoubleWith()
    {

        $selector = $this->profileCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->with('city.country', [IObject::FIELD_IDENTIFY])
            ->with('city', ['name']);

        $sql = $selector->getSelectBuilder()
            ->getSql();

        $this->assertEquals(
            'SELECT `users_profile`.`id` AS `users_profile:id`, `users_profile`.`guid` AS `users_profile:guid`, `users_profile`.`type` AS `users_profile:type`, `users_profile`.`version` AS `users_profile:version`, `users_profile`.`city_id` AS `users_profile:city`, `users_profile:city:country`.`id` AS `users_profile:city:country:id`, `users_profile:city:country`.`guid` AS `users_profile:city:country:guid`, `users_profile:city:country`.`type` AS `users_profile:city:country:type`, `users_profile:city:country`.`version` AS `users_profile:city:country:version`, `users_profile:city`.`id` AS `users_profile:city:id`, `users_profile:city`.`guid` AS `users_profile:city:guid`, `users_profile:city`.`type` AS `users_profile:city:type`, `users_profile:city`.`version` AS `users_profile:city:version`, `users_profile:city`.`name` AS `users_profile:city:name`
FROM `umi_mock_profiles` AS `users_profile`
	LEFT JOIN `umi_mock_cities` AS `users_profile:city` ON `users_profile:city`.`id` = `users_profile`.`city_id`
	LEFT JOIN `umi_mock_countries` AS `users_profile:city:country` ON `users_profile:city:country`.`id` = `users_profile:city`.`country_id`
WHERE 1',
            $sql,
            'Неверный запрос на выборку со связной сущностью c указанными полями'
        );

        $selector->result()
            ->fetch();

    }
}

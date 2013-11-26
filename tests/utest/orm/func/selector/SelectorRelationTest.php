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
use umi\orm\collection\ICollectionFactory;
use umi\orm\object\IObject;
use umi\orm\selector\ISelector;
use utest\orm\ORMDbTestCase;

/**
 * Тест селектора
 *
 */
class SelectorRelationTest extends ORMDbTestCase
{

    public $queries = [];
    protected $user1Guid;
    protected $user2Guid;
    protected $user3Guid;
    protected $blog1Guid;
    protected $blog2Guid;
    protected $blog3Guid;
    protected $profile1Guid;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::SYSTEM_HIERARCHY       => [
                    'type' => ICollectionFactory::TYPE_COMMON_HIERARCHY
                ],
                self::BLOGS_BLOG             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'class'     => 'utest\orm\mock\collections\BlogsCollection',
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::BLOGS_POST             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::BLOGS_SUBSCRIBER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_GROUP            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_PROFILE            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::GUIDES_COUNTRY            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::GUIDES_CITY            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {

        $countryCollection = $this->getCollectionManager()->getCollection(self::GUIDES_COUNTRY);
        $country = $countryCollection->add();
        $country->setValue('name', 'Россия');
        $country2 = $countryCollection->add();
        $country2->setValue('name', 'Германия');

        $cityCollection = $this->getCollectionManager()->getCollection(self::GUIDES_CITY);
        $city1 = $cityCollection->add();
        $city1->setValue('name', 'Санкт-Петербург');
        $city1->setValue('country', $country);

        $city2 = $cityCollection->add();
        $city2->setValue('name', 'Москва');
        $city2->setValue('country', $country);

        $city3 = $cityCollection->add();
        $city3->setValue('name', 'Берлин');
        $city3->setValue('country', $country2);

        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);

        $user1 = $userCollection->add();
        $user1->setValue('login', 'test_login1');
        $user1->setValue('height', 150);
        $this->user1Guid = $user1->getGUID();

        $user2 = $userCollection->add();
        $user2->setValue('login', 'test_login2');
        $user2->setValue('height', 160);
        $this->user2Guid = $user2->getGUID();

        $user3 = $userCollection->add();
        $user3->setValue('login', 'test_login3');
        $user3->setValue('height', 170);
        $this->user3Guid = $user3->getGUID();

        $user4 = $userCollection->add();
        $user4->setValue('login', 'test_login4');

        $user5 = $userCollection->add();
        $user5->setValue('login', 'test_login5');

        $profileCollection = $this->getCollectionManager()->getCollection(self::USERS_PROFILE);

        $profile1 = $profileCollection->add('natural_person');
        $profile1->setValue('name', 'test_name1');
        $profile1->setValue('user', $user3);
        $profile1->setValue('city', $city1);
        $this->profile1Guid = $profile1->getGUID();

        $profile2 = $profileCollection->add('natural_person');
        $profile2->setValue('name', 'test_name2');
        $profile2->setValue('user', $user1);
        $profile2->setValue('city', $city2);

        $profile3 = $profileCollection->add('natural_person');
        $profile3->setValue('name', 'test_name3');
        $profile3->setValue('user', $user4);
        $profile3->setValue('city', $city3);

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog1 = $blogsCollection->add('blog1');
        $blog1->setValue('owner', $user1);
        $blog1->setValue('title', 'first_blog');
        $this->blog1Guid = $blog1->getGUID();

        $blog2 = $blogsCollection->add('blog2');
        $blog2->setValue('owner', $user2);
        $blog2->setValue('title', 'second');
        $this->blog2Guid = $blog2->getGUID();

        $blog3 = $blogsCollection->add('blog3');
        $blog3->setValue('owner', $user2);
        $blog3->setValue('title', 'third');
        $this->blog3Guid = $blog3->getGUID();

        $blog4 = $blogsCollection->add('blog4');
        $blog4->setValue('owner', $user4);
        $blog4->setValue('title', 'forth');
        $this->blog4Guid = $blog4->getGUID();

        $blog5 = $blogsCollection->add('blog5');
        $blog5->setValue('title', 'fifth');
        $this->blog5Guid = $blog5->getGUID();

        $subscribersCollection = $this->getCollectionManager()->getCollection(self::BLOGS_SUBSCRIBER);
        $subscription1 = $subscribersCollection->add();
        $subscription1->setValue('blog', $blog1);
        $subscription1->setValue('user', $user1);

        $subscription2 = $subscribersCollection->add();
        $subscription2->setValue('blog', $blog1);
        $subscription2->setValue('user', $user2);

        $subscription3 = $subscribersCollection->add();
        $subscription3->setValue('blog', $blog1);
        $subscription3->setValue('user', $user3);

        $subscription4 = $subscribersCollection->add();
        $subscription4->setValue('blog', $blog3);
        $subscription4->setValue('user', $user3);

        $subscription5 = $subscribersCollection->add();
        $subscription5->setValue('blog', $blog2);
        $subscription5->setValue('user', $user2);

        $subscription6 = $subscribersCollection->add();
        $subscription6->setValue('blog', $blog4);
        $subscription6->setValue('user', $user4);

        $this->getObjectPersister()->commit();

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

    public function testHasOneWhere()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile.name')
            ->equals('test_name1')
            ->getResult();
        $user = $result->fetch();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
WHERE ((`users_user:profile`.`name` = :value0))"
        ];

        $this->assertEquals($queries, $this->queries, 'Неверный запрос для условия выборки по полю со связью hasOne');

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $user,
            'Ожидается, что первым элементом выборки является объект'
        );
        $this->assertEquals(
            'test_login3',
            $user->getValue('login'),
            'Ожидается, что логин пользователя с именем test_name1 - test_login3'
        );
        $this->assertCount(1, $result->fetchAll(), 'Ожидается, что результат выборки содержит одного пользователя');

    }

    public function testHasOneIsNull()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile')
            ->isNull()
            ->getResult();
        $objects = $result->fetchAll();
        $this->assertCount(2, $objects, 'Ожидается, что у двух пользователей нет профиля');
    }

    public function testHasOneOrderBy()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);

        $result = $userCollection->select()
            ->orderBy('profile.name', ISelector::ORDER_DESC)
            ->getResult();

        $this->assertEquals(
            'test_login4',
            $result->fetch()
                ->getValue('login'),
            'Ожидается, что при обратной сортировке пользователей по имени профиля первым будет пользователь с логином test_login4 (name = test_name3)'
        );
        $this->assertEquals(
            'test_login1',
            $result->fetch()
                ->getValue('login'),
            'Ожидается, что при обратной сортировке пользователей по имени профиля вторым будет пользователь с логином test_login1 (name = test_name2)'
        );
        $this->assertEquals(
            'test_login3',
            $result->fetch()
                ->getValue('login'),
            'Ожидается, что при обратной сортировке пользователей по имени профиля третьим будет пользователь с логином test_login3 (name = test_name1)'
        );
    }

    public function testHasOneObject()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $profileCollection = $this->getCollectionManager()->getCollection(self::USERS_PROFILE);

        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile')
            ->equals($profileCollection->get($this->profile1Guid))
            ->getResult();

        $this->queries = [];
        $user = $result->fetch();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
WHERE ((`users_user:profile`.`id` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью hasOne и объектом в качестве значения'
        );

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $user,
            'Ожидается, что первым элементом выборки является объект'
        );
        $this->assertEquals(
            'test_login3',
            $user->getValue('login'),
            'Ожидается, что логин пользователя, имеющего профиль с id 1 - test_login3'
        );
        $this->assertCount(1, $result->fetchAll(), 'Ожидается, что результат выборки содержит одного пользователя');

    }

    public function testHasOneObjectId()
    {

        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile')
            ->equals(1)
            ->getResult();
        $user = $result->fetch();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
WHERE ((`users_user:profile`.`id` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью hasOne и объектом в качестве значения'
        );

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $user,
            'Ожидается, что первым элементом выборки является объект'
        );
        $this->assertEquals(
            'test_login3',
            $user->getValue('login'),
            'Ожидается, что логин пользователя, имеющего профиль с id 1 - test_login3'
        );
        $this->assertCount(1, $result->fetchAll(), 'Ожидается, что результат выборки содержит одного пользователя');

    }

    public function testHasManyWhere()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('blogs.title')
            ->like('%blog%')
            ->getResult();
        $user = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_blogs` AS `users_user:blogs` ON `users_user:blogs`.`owner_id` = `users_user`.`id`
WHERE ((`users_user:blogs`.`title` LIKE :value0))
GROUP BY `users_user`.`id`"
        ];

        $this->assertEquals($queries, $this->queries, 'Неверный запрос для условия выборки по полю со связью hasMany');
        $this->assertCount(
            1,
            $objects,
            'Ожидается, что один пользователь является владельцем блогов, в имени которых есть слово blog'
        );
        $this->assertEquals(
            $this->user1Guid,
            $user->getGUID(),
            'Ожидается, что пользователь c id 1 является владельцем блогов, в имени которых есть слово blog'
        );

    }

    public function testHasManyIsNull()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('blogs')
            ->isNull()
            ->getResult();
        $objects = $result->fetchAll();

        $this->assertCount(2, $objects, 'Ожидается, что два пользователя не являются авторами блогов');
    }

    public function testHasManyObject()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('blogs')
            ->equals($blogsCollection->get($this->blog1Guid))
            ->getResult();
        $this->queries = [];
        $user = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_blogs` AS `users_user:blogs` ON `users_user:blogs`.`owner_id` = `users_user`.`id`
WHERE ((`users_user:blogs`.`id` = :value0))
GROUP BY `users_user`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью hasMany и объектом в качестве значения'
        );
        $this->assertCount(1, $objects, 'Ожидается, что один пользователь является владельцем блога с id 1');
        $this->assertEquals(
            $this->user1Guid,
            $user->getGUID(),
            'Ожидается, что пользователь c id 1 является владельцем блога с id 1'
        );

    }

    public function testHasManyObjectId()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('blogs')
            ->equals(1)
            ->getResult();
        $user = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_blogs` AS `users_user:blogs` ON `users_user:blogs`.`owner_id` = `users_user`.`id`
WHERE ((`users_user:blogs`.`id` = :value0))
GROUP BY `users_user`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью hasMany и id объекта в качестве значения'
        );
        $this->assertCount(1, $objects, 'Ожидается, что один пользователь является владельцем блога с id 1');
        $this->assertEquals(
            $this->user1Guid,
            $user->getGUID(),
            'Ожидается, что пользователь c id 1 является владельцем блога с id 1'
        );
    }

    public function testBelongsToWhere()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('owner.login')
            ->equals('test_login2')
            ->getResult();
        $blog1 = $result->fetch();
        $blog2 = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:owner` ON `blogs_blog:owner`.`id` = `blogs_blog`.`owner_id`
WHERE ((`blogs_blog:owner`.`login` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью belongsTo'
        );
        $this->assertCount(2, $objects, 'Ожидается, что пользователь с логином test_login2 владеет двумя блогами');
        $this->assertEquals(
            $this->blog2Guid,
            $blog1->getGUID(),
            'Ожидается, что пользователь с логином test_login2 владеет блогом с id 2'
        );
        $this->assertEquals(
            $this->blog3Guid,
            $blog2->getGUID(),
            'Ожидается, что пользователь с логином test_login2 владеет блогом с id 3'
        );
    }

    public function testBelongsToIsNull()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('owner')
            ->isNull()
            ->getResult();
        $objects = $result->fetchAll();

        $this->assertCount(1, $objects, 'Ожидается, что только у одного блога нет владельца');
    }

    public function testBelongsToObject()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('owner')
            ->equals($userCollection->get($this->user1Guid))
            ->getResult();
        $this->queries = [];
        $blog = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:owner` ON `blogs_blog:owner`.`id` = `blogs_blog`.`owner_id`
WHERE ((`blogs_blog:owner`.`id` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью belongsTo и объектом в качестве значения'
        );
        $this->assertCount(1, $objects, 'Ожидается, что пользователю с id 1 один принадлежит блог');
        $this->assertEquals(
            $this->blog1Guid,
            $blog->getGUID(),
            'Ожидается, что пользователю с id 1 принадлежит блог с id 1'
        );
    }

    public function testBelongsToObjectId()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('owner')
            ->equals(1)
            ->getResult();
        $blog = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:owner` ON `blogs_blog:owner`.`id` = `blogs_blog`.`owner_id`
WHERE ((`blogs_blog:owner`.`id` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью belongsTo и id объекта в качестве значения'
        );
        $this->assertCount(1, $objects, 'Ожидается, что пользователю с id 1 один принадлежит блог');
        $this->assertEquals(
            $this->blog1Guid,
            $blog->getGUID(),
            'Ожидается, что пользователю с id 1 принадлежит блог с id 1'
        );
    }

    public function testManyToManyWhere()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('subscribers.height')
            ->more('169')
            ->getResult();
        $blog1 = $result->fetch();
        $blog2 = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_blog_subscribers` AS `blogs_blog:subscribers_bridge` ON `blogs_blog:subscribers_bridge`.`blog_id` = `blogs_blog`.`id`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:subscribers` ON `blogs_blog:subscribers`.`id` = `blogs_blog:subscribers_bridge`.`user_id`
WHERE ((`blogs_blog:subscribers`.`height` > :value0))
GROUP BY `blogs_blog`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью manyToMany'
        );
        $this->assertCount(2, $objects, 'Ожидается, что пользователь с ростом более 169 подписаны на 2 блога');
        $this->assertEquals(
            $this->blog1Guid,
            $blog1->getGUID(),
            'Ожидается, что пользователи с ростом более 169 подписаны на блог с id 1'
        );
        $this->assertEquals(
            $this->blog3Guid,
            $blog2->getGUID(),
            'Ожидается, что пользователи с ростом более 169 подписаны на блог с id 3'
        );

    }

    public function testManyToManyIsNull()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('subscribers')
            ->isNull()
            ->getResult();
        $objects = $result->fetchAll();

        $this->assertCount(1, $objects, 'Ожидается, что только у одного блога нет подписчиков');

    }

    public function testManyToManyObject()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('subscribers')
            ->equals($userCollection->get($this->user3Guid))
            ->getResult();
        $this->queries = array();
        $blog1 = $result->fetch();
        $blog2 = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_blog_subscribers` AS `blogs_blog:subscribers_bridge` ON `blogs_blog:subscribers_bridge`.`blog_id` = `blogs_blog`.`id`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:subscribers` ON `blogs_blog:subscribers`.`id` = `blogs_blog:subscribers_bridge`.`user_id`
WHERE ((`blogs_blog:subscribers`.`id` = :value0))
GROUP BY `blogs_blog`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью manyToMany'
        );
        $this->assertCount(2, $objects, 'Ожидается, что пользователь с id 3 подписан на 2 блога');
        $this->assertEquals(
            $this->blog1Guid,
            $blog1->getGUID(),
            'Ожидается, что пользователь с id 3 подписан на блог с id 1'
        );
        $this->assertEquals(
            $this->blog3Guid,
            $blog2->getGUID(),
            'Ожидается, что пользователь с id 3 подписан на блог с id 3'
        );
    }

    public function testManyToManyObjectId()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('subscribers')
            ->equals(3)
            ->getResult();
        $this->queries = array();
        $blog1 = $result->fetch();
        $blog2 = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_blog_subscribers` AS `blogs_blog:subscribers_bridge` ON `blogs_blog:subscribers_bridge`.`blog_id` = `blogs_blog`.`id`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:subscribers` ON `blogs_blog:subscribers`.`id` = `blogs_blog:subscribers_bridge`.`user_id`
WHERE ((`blogs_blog:subscribers`.`id` = :value0))
GROUP BY `blogs_blog`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью manyToMany'
        );
        $this->assertCount(2, $objects, 'Ожидается, что пользователь с id 3 подписан на 2 блога');
        $this->assertEquals(
            $this->blog1Guid,
            $blog1->getGUID(),
            'Ожидается, что пользователь с id 3 подписан на блог с id 1'
        );
        $this->assertEquals(
            $this->blog3Guid,
            $blog2->getGUID(),
            'Ожидается, что пользователь с id 3 подписан на блог с id 3'
        );
    }

    public function testRelationExceptions()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $selector = $blogsCollection->select();

        $e = null;
        try {
            $selector
                ->where('subscribers.profle.name')
                ->equals('test_name2');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при наличии в пути к полю несуществующего поля'
        );

        $usersCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $selector = $usersCollection->select();

        $e = null;
        try {
            $selector
                ->where('height.profile')
                ->equals(1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при наличии в середине пути поля с типом, отличным от relation '
        );

    }

    /**
     * Тест селектора, когда выборка идет вглубь от коллекции, связанной с коллекцией селектора по полю с типом связи manyToMany
     */
    public function testThroughManyToMany()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('subscribers.profile.name')
            ->equals('test_name2')
            ->getResult();
        $blog = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_blog_subscribers` AS `blogs_blog:subscribers_bridge` ON `blogs_blog:subscribers_bridge`.`blog_id` = `blogs_blog`.`id`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:subscribers` ON `blogs_blog:subscribers`.`id` = `blogs_blog:subscribers_bridge`.`user_id`
	LEFT JOIN `umi_mock_profiles` AS `blogs_blog:subscribers:profile` ON `blogs_blog:subscribers:profile`.`user_id` = `blogs_blog:subscribers`.`id`
WHERE ((`blogs_blog:subscribers:profile`.`name` = :value0))
GROUP BY `blogs_blog`.`id`"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью manyToMany'
        );
        $this->assertCount(1, $objects, 'Ожидается, что пользователь с именем test_name2 подписан на 1 блог');
        $this->assertEquals(
            $this->blog1Guid,
            $blog->getGUID(),
            'Ожидается, что пользователь с именем test_name2 подписан на блог c id 1'
        );

    }

    /**
     * Тест селектора, когда выборка идет вглубь от коллекции, связанной с коллекцией селектора по полю с типом связи belongsTo
     */
    public function testThroughBelongsTo()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $result = $blogsCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('owner.profile.name')
            ->equals('test_name2')
            ->getResult();
        $blog = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`
FROM `umi_mock_blogs` AS `blogs_blog`
	LEFT JOIN `umi_mock_users` AS `blogs_blog:owner` ON `blogs_blog:owner`.`id` = `blogs_blog`.`owner_id`
	LEFT JOIN `umi_mock_profiles` AS `blogs_blog:owner:profile` ON `blogs_blog:owner:profile`.`user_id` = `blogs_blog:owner`.`id`
WHERE ((`blogs_blog:owner:profile`.`name` = :value0))"
        ];

        $this->assertEquals(
            $queries,
            $this->queries,
            'Неверный запрос для условия выборки по полю со связью belongsTo'
        );
        $this->assertCount(1, $objects, 'Ожидается, что пользователь с именем test_name2 является владельцем 1 блога');
        $this->assertEquals(
            $this->blog1Guid,
            $blog->getGUID(),
            'Ожидается, что пользователь с именем test_name2 является владельцем блога c id 1'
        );
    }

    /**
     * Тест селектора, когда выборка идет вглубь от коллекции, связанной с коллекцией селектора по полю с типом связи hasOne
     */
    public function testThroughHasOne()
    {

        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile.city.country.name')
            ->equals('Россия')
            ->getResult();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
	LEFT JOIN `umi_mock_cities` AS `users_user:profile:city` ON `users_user:profile:city`.`id` = `users_user:profile`.`city_id`
	LEFT JOIN `umi_mock_countries` AS `users_user:profile:city:country` ON `users_user:profile:city:country`.`id` = `users_user:profile:city`.`country_id`
WHERE ((`users_user:profile:city:country`.`name` = :value0))"
        ];

        $this->assertEquals($queries, $this->queries, 'Неверный запрос для условия выборки по полю со связью hasOne');
        $this->assertCount(2, $objects, 'Ожидается, что 2 пользователя имеют профили из России');
        $this->assertContains(
            $userCollection->get($this->user1Guid),
            $objects,
            'Ожидается, что пользователи c id 3 и 1 имеют профиль из России'
        );
        $this->assertContains(
            $userCollection->get($this->user3Guid),
            $objects,
            'Ожидается, что пользователи c id 3 и 1 имеют профиль из России'
        );

    }

    /**
     * Тест селектора, когда выборка идет вглубь от коллекции, связанной с коллекцией селектора по полю с типом связи hasMany
     */
    public function testThroughHasMany()
    {

        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('blogs.subscribers.profile.city.country.name')
            ->equals('Россия')
            ->getResult();
        $user1 = $result->fetch();
        $user2 = $result->fetch();
        $objects = $result->fetchAll();

        $queries = [
            "SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_blogs` AS `users_user:blogs` ON `users_user:blogs`.`owner_id` = `users_user`.`id`
	LEFT JOIN `umi_mock_blog_subscribers` AS `users_user:blogs:subscribers_bridge` ON `users_user:blogs:subscribers_bridge`.`blog_id` = `users_user:blogs`.`id`
	LEFT JOIN `umi_mock_users` AS `users_user:blogs:subscribers` ON `users_user:blogs:subscribers`.`id` = `users_user:blogs:subscribers_bridge`.`user_id`
	LEFT JOIN `umi_mock_profiles` AS `users_user:blogs:subscribers:profile` ON `users_user:blogs:subscribers:profile`.`user_id` = `users_user:blogs:subscribers`.`id`
	LEFT JOIN `umi_mock_cities` AS `users_user:blogs:subscribers:profile:city` ON `users_user:blogs:subscribers:profile:city`.`id` = `users_user:blogs:subscribers:profile`.`city_id`
	LEFT JOIN `umi_mock_countries` AS `users_user:blogs:subscribers:profile:city:country` ON `users_user:blogs:subscribers:profile:city:country`.`id` = `users_user:blogs:subscribers:profile:city`.`country_id`
WHERE ((`users_user:blogs:subscribers:profile:city:country`.`name` = :value0))
GROUP BY `users_user`.`id`"
        ];

        $this->assertEquals($queries, $this->queries, 'Неверный запрос для условия выборки по полю со связью hasOne');
        $this->assertCount(
            2,
            $objects,
            'Ожидается, что два пользователя являются владельцами блогов, подписчиками которых являются пользователи из России'
        );
        $this->assertEquals(
            $this->user1Guid,
            $user1->getGUID(),
            'Ожидается, что пользователь с id 1 является владельцем блогов, подписчиками которых являются пользователи из России'
        );
        $this->assertEquals(
            $this->user2Guid,
            $user2->getGUID(),
            'Ожидается, что пользователь с id 2 является владельцем блогов, подписчиками которых являются пользователи из России'
        );

    }

    public function testDoubleWhere()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile.name')
            ->equals('test_name2')
            ->where('profile.city.name')
            ->equals('Москва')
            ->getResult();
        $user = $result->fetch();

        $queries = [
            'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
	LEFT JOIN `umi_mock_cities` AS `users_user:profile:city` ON `users_user:profile:city`.`id` = `users_user:profile`.`city_id`
WHERE ((`users_user:profile`.`name` = :value0 AND `users_user:profile:city`.`name` = :value1))'
        ];

        $this->assertEquals($queries, $this->queries, 'Неверный запрос для условия выборки с двойным сложным where');

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $user,
            'Ожидается, что первым элементом выборки является объект'
        );
        $this->assertEquals(
            'test_login1',
            $user->getValue('login'),
            'Ожидается, что логин пользователя с именем test_name2 и городом Берлин - test_login1'
        );
        $this->assertCount(1, $result->fetchAll(), 'Ожидается, что результат выборки содержит одного пользователя');

    }

    public function testOrderBy()
    {
        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $result = $userCollection->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where('profile')
            ->notNull()
            ->orderBy('profile.name')
            ->getResult();

        $user1 = $result->fetch();
        $this->assertEquals(3, $user1->getId());

        $user2 = $result->fetch();
        $this->assertEquals(1, $user2->getId());

        $user3 = $result->fetch();
        $this->assertEquals(4, $user3->getId());

        $this->assertEquals(
            [
                'SELECT `users_user`.`id` AS `users_user:id`, `users_user`.`guid` AS `users_user:guid`, `users_user`.`type` AS `users_user:type`, `users_user`.`version` AS `users_user:version`
FROM `umi_mock_users` AS `users_user`
	LEFT JOIN `umi_mock_profiles` AS `users_user:profile` ON `users_user:profile`.`user_id` = `users_user`.`id`
WHERE ((`users_user:profile`.`id` IS NOT :value0))
ORDER BY `users_user:profile`.`name` ASC'
            ],
            $this->queries,
            'Неверный запрос'
        );
    }

}

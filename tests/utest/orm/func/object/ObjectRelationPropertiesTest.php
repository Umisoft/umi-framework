<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\orm\objectset\IObjectSet;
use utest\orm\ORMDbTestCase;

/**
 * Тесты полей объекта типа relation
 */
class ObjectRelationPropertiesTest extends ORMDbTestCase
{
    protected $userGuid;
    protected $user3Guid;
    protected $groupGuid;
    protected $profileGuid;

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
            self::BLOGS_BLOG,
            self::BLOGS_POST,
            self::BLOGS_SUBSCRIBER,
        ];
    }

    protected function setUpFixtures()
    {

        $groupCollection = $this->collectionManager->getCollection(self::USERS_GROUP);

        $group1 = $groupCollection->add();
        $group1->setValue('name', 'test_group1');

        $group2 = $groupCollection->add();
        $group2->setValue('name', 'test_group2');

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $user1 = $userCollection->add();
        $user1->setValue('login', 'test_login');
        $user1->setValue('group', $group2);

        $user2 = $userCollection->add();
        $user2->setValue('login', 'test_login');
        $user2->setValue('group', $group2);

        $user3 = $userCollection->add();

        $profileCollection = $this->collectionManager->getCollection(self::USERS_PROFILE);

        $profile1 = $profileCollection->add('natural_person');
        $profile1->setValue('name', 'test_name1');

        $profile2 = $profileCollection->add('natural_person');
        $profile2->setValue('name', 'test_name2');
        $profile2->setValue('user', $user1);

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->add('first_blog');
        $blog1->setValue('owner', $user1);
        $blog1->setValue('title', 'first_blog');

        $blog2 = $blogsCollection->add('second');
        $blog2->setValue('owner', $user2);
        $blog2->setValue('title', 'second');

        $subscribersCollection = $this->collectionManager->getCollection(self::BLOGS_SUBSCRIBER);
        $subscription1 = $subscribersCollection->add();
        $subscription1->setValue('blog', $blog1);
        $subscription1->setValue('user', $user1);

        $subscription2 = $subscribersCollection->add();
        $subscription2->setValue('blog', $blog2);
        $subscription2->setValue('user', $user1);

        $this->objectPersister->commit();

        $this->groupGuid = $group2->getGUID();
        $this->userGuid = $user1->getGUID();
        $this->profileGuid = $profile2->getGUID();
        $this->user3Guid = $user3->getGUID();

        $user1->unload();
        $group2->unload();
    }

    public function testBelongsToProperty()
    {
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $loadedUser = $userCollection->get($this->userGuid);
        $userGroup = $loadedUser->getValue('group');

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $userGroup,
            'Ожидается, что значением свойства group является объект'
        );
        $this->assertEquals(
            $userGroup->getGUID(),
            $this->groupGuid,
            'Ожидается, что guid объекта, являющегося значением поля group, совпадает с guid созданного объекта'
        );

        $loadedUser3 = $userCollection->get($this->user3Guid);
        $this->assertNull(
            $loadedUser3->getValue('group'),
            'Ожидается, что если в поле belongsTo нет значения, зхначением свойства будет null'
        );

        $loadedUser->setValue('group', null);
        $this->assertNull(
            $loadedUser->getValue('group'),
            'Ожидается, что значение свойства belongsTo можно сбросить в null'
        );

        $e = null;
        try {
            $loadedUser->setValue('group', 1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выставить в поле belongsTo не объект'
        );

        $e = null;
        try {
            $loadedUser->setValue('group', $loadedUser);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выставить в поле belongsTo объект из неверной коллекции'
        );

    }

    public function testHasManyProperty()
    {
        $groupCollection = $this->collectionManager->getCollection(self::USERS_GROUP);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $loadedGroup = $groupCollection->get($this->groupGuid);
        /**
         * @var IObjectSet $groupUsers
         */
        $groupUsers = $loadedGroup->getValue('users');
        $this->assertInstanceOf(
            'umi\orm\objectset\IObjectSet',
            $groupUsers,
            'Ожидается, что значением свойства users является IObjectSet'
        );
        $this->assertSame(
            $userCollection->get($this->userGuid),
            $groupUsers->fetch(),
            'Ожидается, что первый пользователь в группе это первый созданный пользователь'
        );
        $this->assertCount(2, $groupUsers->fetchAll(), 'Ожидается, что в группе 2 пользователя');

        $e = null;
        try {
            $loadedGroup->setValue('users', 1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что нельзя напрямую установить значение для свойства hasMany'
        );

    }

    public function testHasOneRelation()
    {
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $loadedUser = $userCollection->get($this->userGuid);
        $userProfile = $loadedUser->getValue('profile');

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $userProfile,
            'Ожидается, что значением свойства profile является объект'
        );
        $this->assertEquals(
            $userProfile->getGUID(),
            $this->profileGuid,
            'Ожидается, что id объекта, являющегося значением поля profile, совпадает с id созданного объекта'
        );
    }

    public function testManyToManyRelation()
    {
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $loadedUser = $userCollection->get($this->userGuid);
        $subscriptions = $loadedUser->getValue('subscription');

        $this->assertInstanceOf(
            'umi\orm\objectset\IManyToManyObjectSet',
            $subscriptions,
            'Ожидается, что значением свойства subscriptions является IObjectSet'
        );
        $this->assertCount(2, $subscriptions->fetchAll(), 'Ожидается, что пользователь подписан на 2 блога');

        $e = null;
        try {
            $loadedUser->setValue('subscription', 1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что нельзя напрямую установить значение для свойства ManyToMany'
        );
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\objectset;

use umi\orm\object\IObject;
use umi\orm\objectset\IManyToManyObjectSet;
use utest\orm\ORMDbTestCase;

/**
 * Тест класса ManyToManyObjectSet
 */
class ManyToManyObjectSetTest extends ORMDbTestCase
{

    protected $user1Guid;
    protected $user2Guid;
    protected $user3Guid;
    protected $blog1Guid;
    protected $blog2Guid;


    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::SYSTEM_HIERARCHY,
            self::USERS_GROUP,
            self::USERS_USER,
            self::BLOGS_BLOG,
            self::BLOGS_POST,
            self::BLOGS_SUBSCRIBER,
        ];
    }

    protected function setUpFixtures()
    {
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $user1 = $userCollection->add();
        $user1->setValue('login', 'test_login1');
        $this->user1Guid = $user1->getGUID();

        $user2 = $userCollection->add();
        $user2->setValue('login', 'test_login2');
        $this->user2Guid = $user2->getGUID();

        $user3 = $userCollection->add();
        $user3->setValue('login', 'test_login3');
        $this->user3Guid = $user3->getGUID();

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog1 = $blogsCollection->add('blog1');
        $blog1->setValue('owner', $user1);
        $blog1->setValue('title', 'first_blog');
        $this->blog1Guid = $blog1->getGUID();

        $blog2 = $blogsCollection->add('blog2');
        $blog2->setValue('owner', $user1);
        $blog2->setValue('title', 'first_blog');
        $this->blog2Guid = $blog2->getGUID();

        $subscribersCollection = $this->collectionManager->getCollection(self::BLOGS_SUBSCRIBER);

        $subscription1 = $subscribersCollection->add();
        $subscription1->setValue('blog', $blog1);
        $subscription1->setValue('user', $user1);

        $subscription2 = $subscribersCollection->add();
        $subscription2->setValue('blog', $blog1);
        $subscription2->setValue('user', $user2);

        $this->objectPersister->commit();
    }

    public function testManyToManyProperty()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog = $blogsCollection->get($this->blog1Guid);

        $e = null;
        try {
            $blog->getProperty('subscribers')
                ->setValue(1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается исключение при попытке уставновить значения для поля с типом связи manyToMany'
        );

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog->getValue('subscribers');
        $this->assertInstanceOf(
            'umi\orm\objectset\IManyToManyObjectSet',
            $subscribers,
            'Ожидается, что значение для поля со связью manyToMany содержится в IManyToManyObjectSet'
        );
        $this->assertCount(2, $subscribers->fetchAll(), 'Ожидается, что значение содержит 2 объекта');

    }

    public function testContains()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user1 = $userCollection->get($this->user1Guid);
        $user2 = $userCollection->get($this->user2Guid);
        $user3 = $userCollection->get($this->user3Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');

        $this->assertFalse(
            $subscribers->contains($user3),
            'Ожидается, что третий пользователь не является подписчиком блога'
        );

        // Проверяем, существовал ли объект
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Неверные запросы на проверку наличия объекта в ObjectSet, когда objectsSet не был загружен до конца'
        );

        $this->resetQueries();
        $this->assertTrue(
            $subscribers->contains($user1),
            'Ожидается, что первый пользователь является подписчиком блога'
        );
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Неверные запросы на проверку наличия объекта в ObjectSet, когда objectsSet не был загружен до конца'
        );

        $this->resetQueries();
        $this->assertTrue(
            $subscribers->contains($user1),
            'Ожидается, что первый пользователь является подписчиком блога'
        );
        $this->assertEquals(
            [],
            $this->getQueries(),
            'Ожидается, что запросы не будут выполнены на проверку наличия объекта в ObjectSet, когда objectsSet не был загружен до конца, но связанный объект уже был загружен'
        );

        $subscribers->fetchAll();
        $this->resetQueries();
        $this->assertTrue(
            $subscribers->contains($user2),
            'Ожидается, что второй пользователь является подписчиком блога'
        );
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Неверные запросы на проверку наличия объекта в ObjectSet, когда objectsSet был загружен до конца'
        );

        $this->resetQueries();
        $this->assertFalse(
            $subscribers->contains($user3),
            'Ожидается, что третий пользователь не является подписчиком блога'
        );
        $this->assertEquals(
            [],
            $this->getQueries(),
            'Ожидается, что никакие запросы не будут выполнены на проверку наличия объекта в ObjectSet, когда objectsSet загружен до конца'
        );

    }

    public function testAttachWrongObject()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $blog2 = $blogsCollection->get($this->blog2Guid);
        $user1 = $userCollection->get($this->user1Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $e = null;
        try {
            $subscribers->attach($blog2);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке добавить в objectsSet объект неподходящей коллекции'
        );
        $e = null;
        try {
            $subscribers->attach($user1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\AlreadyExistentEntityException',
            $e,
            'Ожидается исключение при попытке добавить в objectsSet уже существующий объект'
        );
    }

    public function testAttachExistingObjectWithoutFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user3 = $userCollection->get($this->user3Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscriberLink = $subscribers->attach($user3);
        $this->objectPersister->commit();

        //todo! power up assertion
        $this->assertTrue(
            count($this->getOnlyQueries('select'))==1 && count($this->getOnlyQueries('insert'))==1,
            'Неверные запросы на добавление существующего объекта'
        );
        $this->assertCount(3, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 3 подписчика');

        $this->assertEquals(
            self::BLOGS_SUBSCRIBER,
            $subscriberLink->getCollection()
                ->getName(),
            'Ожидается, что при добавлении в ObjectSet вернется объект bridge-коллекции'
        );
    }

    public function testAttachExistingObjectWithFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user3 = $userCollection->get($this->user3Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscribers->fetchAll();
        $subscribers->attach($user3);
        $this->objectPersister->commit();

        $this->assertCount(3, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 3 подписчика');
    }

    public function testAttachExistingObjectWithPartlyFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user3 = $userCollection->get($this->user3Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $this->assertEquals(
            1,
            $subscribers->fetch()
                ->getId()
        );
        $subscribers->attach($user3);
        $this->objectPersister->commit();
        $this->assertEquals(
            3,
            $subscribers->fetch()
                ->getId()
        );
        $this->assertEquals(
            2,
            $subscribers->fetch()
                ->getId()
        );
        $this->assertNull($subscribers->fetch());

        $this->assertCount(3, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 3 подписчика');
    }

    public function testAttachNewObject()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user4 = $userCollection->add();
        $user4->setValue('login', 'test_login4');

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscriberLink = $subscribers->attach($user4);
        $this->objectPersister->commit();

        $this->assertTrue(
            2 == count($this->getOnlyQueries('insert')) && 1 == count($this->getOnlyQueries('update')),
            'Неверные запросы на добавление нового объекта'
        );
        $this->assertCount(3, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 3 подписчика');

        $this->assertEquals(
            self::BLOGS_SUBSCRIBER,
            $subscriberLink->getCollection()
                ->getName(),
            'Ожидается, что при добавлении в ObjectSet вернется объект bridge-коллекции'
        );
    }

    public function testLinkObject()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user1 = $userCollection->get($this->user1Guid);
        $user4 = $userCollection->add();
        $user4->setValue('login', 'test_login4');

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $linkObject = $subscribers->link($user1);

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $linkObject,
            'Ожидается, что IManyToManyObjectSet::link() вернет связанный объект, '
            . 'когда добавляется уже существующий в сете объект'
        );
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $subscribers->link($user4),
            'Ожидается, что IManyToManyObjectSet::link() вернет связанный объект, '
            . 'когда добавляется еще не существующий в сете объект'
        );

        $this->assertTrue($subscribers->contains($user4), 'Ожидается, что после аттача объект находится в сете');
    }

    public function testDetachExistingObjectWithoutFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user2 = $userCollection->get($this->user2Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscribers->detach($user2);
        $this->objectPersister->commit();

        $this->assertTrue(
            count($this->getOnlyQueries('select'))==1 && count($this->getOnlyQueries('delete'))==1,
            'Неверные запросы на удалениие связанного объекта'
        );
        $this->assertCount(1, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 1 подписчик');
    }

    public function testDetachExistingObjectWithFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user2 = $userCollection->get($this->user2Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscribers->fetchAll();
        $subscribers->detach($user2);
        $this->objectPersister->commit();

        $this->assertCount(1, $subscribers->fetchAll(), 'Ожидается, что теперь у блога 1 подписчик');
    }

    public function testDetachExistingObjectWithPartlyFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $blog1 = $blogsCollection->get($this->blog1Guid);
        $user2 = $userCollection->get($this->user2Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $this->assertEquals(
            1,
            $subscribers->fetch()
                ->getId()
        );
        $subscribers->detach($user2);
        $this->objectPersister->commit();
        $this->assertNull($subscribers->fetch());
        $result = $subscribers->fetchAll();

        $this->assertCount(1, $result, 'Ожидается, что теперь у блога 1 подписчик');
        /**
         * @var IObject $user1 ;
         */
        $user1 = $result[0];
        $this->assertEquals(1, $user1->getId(), 'Ожидается, что единственный подписчик блога это пользователь с id 1');
    }

    public function testDetachAllBeforeFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->get($this->blog1Guid);

        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscribers->detachAll();
        $this->resetQueries();
        $this->objectPersister->commit();
        $this->assertEquals(
            2,
            count($this->getOnlyQueries('delete')),
            'Неверные запросы на удалениие связанного объекта'
        );
        $this->assertCount(0, $subscribers->fetchAll(), 'Ожидается, что теперь у блога нет подписчиков');

    }

    public function testDetachAllAfterFetch()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->get($this->blog1Guid);
        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $this->assertCount(2, $subscribers->fetchAll(), 'Ожидается, что у блога изначально 2 подписчика');
        $subscribers->detachAll();
        $this->resetQueries();
        $this->objectPersister->commit();
        $this->assertCount(0, $subscribers->fetchAll(), 'Ожидается, что теперь у блога нет подписчиков');

    }

    public function testReset()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->get($this->blog1Guid);
        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog1->getValue('subscribers');
        $subscribers->fetchAll();
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что был выполнен запрос на получение подписчиков'
        );
        $this->resetQueries();

        $subscribers = $blog1->getValue('subscribers');
        $subscribers->fetchAll();
        $this->assertEmpty($this->getQueries(), 'Ожидается, что повторного запроса на получение подписчиков не будет');
        $subscribers->reset();

        $subscribers = $blog1->getValue('subscribers');
        $subscribers->fetchAll();
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что после ресета объекты будут снова загружены из базы'
        );
    }

    public function testAttachForNewObject()
    {

        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $firstUser = $userCollection->add()
            ->setValue('login', '0');
        $secondUser = $userCollection->add()
            ->setValue('login', '1');

        $blog = $blogsCollection->add('newBlog');
        $blogGuid = $blog->getGUID();
        /**
         * @var $subscribers IManyToManyObjectSet
         */
        $subscribers = $blog->getValue('subscribers');
        $subscribers->attach($firstUser);
        $subscribers->attach($secondUser);

        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();

        $subscribers = $blogsCollection->get($blogGuid)
            ->getValue('subscribers');
        $i = 0;

        /**
         * @var $subscriber IObject
         */
        foreach ($subscribers as $subscriber) {
            $this->assertEquals($i, $subscriber->getValue('login'));
            $i++;
        }

    }

}

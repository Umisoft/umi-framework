<?php
use umi\orm\metadata\IObjectType;
use utest\orm\ORMDbTestCase;

/**
 * Тест иерархической коллекции
 */
class LinkedCollectionPersistQueriesTest extends ORMDbTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return array(
            self::USERS_GROUP,
            self::USERS_USER,
            self::SYSTEM_HIERARCHY,
            self::BLOGS_BLOG,
            self::BLOGS_POST,
        );
    }

    public function testAdd()
    {

        $this->resetQueries();

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->collectionManager->getCollection(self::BLOGS_POST);

        $blog1 = $blogsCollection->add('test_blog');
        $blog1->setValue('title', 'test_blog');

        $post1 = $postsCollection->add('test_post', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post');

        $this->objectPersister->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "slug", "child_count", "title" ) VALUES ( :type, :guid, :slug, :child_count, :title )',
            'INSERT INTO "umi_mock_blogs"
( "id", "type", "guid", "slug", "child_count", "title" ) VALUES ( :id, :type, :guid, :slug, :child_count, :title )',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "pid", "slug", "title" ) VALUES ( :type, :guid, :pid, :slug, :title )',
            'INSERT INTO "umi_mock_posts"
( "id", "type", "guid", "pid", "slug", "title" ) VALUES ( :id, :type, :guid, :pid, :slug, :title )',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" IS :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_blogs"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_hierarchy"
SET "pid" = :pid, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" = :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_posts"
SET "version" = "version" + (1), "pid" = :pid
WHERE "id" = :objectId AND "version" = :version',
            'UPDATE "umi_mock_posts"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы при добавлении иерархических объектов'
        );
    }

    public function testAddAndUpdate()
    {

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog = $blogsCollection->add('first_blog');
        $blog->setValue('title', 'first_blog');
        $this->objectPersister->commit();
        $this->resetQueries();

        $postsCollection = $this->collectionManager->getCollection(self::BLOGS_POST);
        $post = $postsCollection->add('test_post', IObjectType::BASE, $blog);
        $post->setValue('title', 'test_post');

        $this->objectPersister->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "pid", "slug", "title" ) VALUES ( :type, :guid, :pid, :slug, :title )',
            'INSERT INTO "umi_mock_posts"
( "id", "type", "guid", "pid", "slug", "title" ) VALUES ( :id, :type, :guid, :pid, :slug, :title )',
            'UPDATE "umi_mock_hierarchy"
SET "child_count" = "child_count" + (1)
WHERE "id" = :objectId',
            'UPDATE "umi_mock_blogs"
SET "child_count" = "child_count" + (1)
WHERE "id" = :objectId',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" = :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_posts"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы при добавлении дочернего объекта к уже существующему родителю'
        );

    }

    public function testModify()
    {

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->add('first_blog');
        $blog1->setValue('title', 'first_blog');
        $blog1Guid = $blog1->getGUID();
        $this->objectPersister->commit();
        $this->resetQueries();

        $blog = $blogsCollection->get($blog1Guid);
        $blog->setValue('title', 'new_title');

        $this->objectPersister->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'UPDATE "umi_mock_hierarchy"
SET "title" = :title, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            'UPDATE "umi_mock_blogs"
SET "version" = "version" + (1), "title" = :title
WHERE "id" = :objectId AND "version" = :version',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы при изменении иерархических объектов'
        );

        $this->resetQueries();
        $blog->setValue('publishTime', '13.08.13');
        $this->objectPersister->commit();
        $expectedResult = [
            '"START TRANSACTION"',
            'UPDATE "umi_mock_blogs"
SET "publish_time" = :publish_time, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы при изменении неиерархических свойств иерархических объектов'
        );
    }

    public function testDelete()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->add('first_blog');
        $blog1->setValue('title', 'first_blog');
        $blog1Guid = $blog1->getGUID();
        $this->objectPersister->commit();
        $this->resetQueries();

        $blog = $blogsCollection->get($blog1Guid);
        $blogsCollection->delete($blog);
        $this->objectPersister->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'DELETE FROM "umi_mock_hierarchy"
WHERE "id" = :objectId',
            'DELETE FROM "umi_mock_blogs"
WHERE "id" = :objectId',
            '"COMMIT"'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы при удалении иерархических объектов'
        );
    }

}

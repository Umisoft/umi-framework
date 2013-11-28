<?php
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\cluster\IConnection;
use umi\event\IEvent;
use umi\orm\collection\ICollectionFactory;
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
                ]
            ],
            true
        ];
    }

    public function testAdd()
    {

        $this->resetQueries();

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);

        $blog1 = $blogsCollection->add('test_blog');
        $blog1->setValue('title', 'test_blog');

        $post1 = $postsCollection->add('test_post', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post');

        $this->getObjectPersister()->commit();

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

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $blog = $blogsCollection->add('first_blog');
        $blog->setValue('title', 'first_blog');
        $this->getObjectPersister()->commit();
        $this->resetQueries();

        $postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);
        $post = $postsCollection->add('test_post', IObjectType::BASE, $blog);
        $post->setValue('title', 'test_post');

        $this->getObjectPersister()->commit();

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

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->add('first_blog');
        $blog1->setValue('title', 'first_blog');
        $blog1Guid = $blog1->getGUID();
        $this->getObjectPersister()->commit();
        $this->resetQueries();

        $blog = $blogsCollection->get($blog1Guid);
        $blog->setValue('title', 'new_title');

        $this->getObjectPersister()->commit();

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
        $this->getObjectPersister()->commit();
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
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $blog1 = $blogsCollection->add('first_blog');
        $blog1->setValue('title', 'first_blog');
        $blog1Guid = $blog1->getGUID();
        $this->getObjectPersister()->commit();
        $this->resetQueries();

        $blog = $blogsCollection->get($blog1Guid);
        $blogsCollection->delete($blog);
        $this->getObjectPersister()->commit();

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

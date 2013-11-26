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

    public $queries = [];

    protected $usedDbServerId = 'mysqlMaster';

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

    protected function setUpFixtures()
    {
        $this->queries = [];
        $self = $this;
        $this->getDbCluster()
            ->getDbDriver()
            ->bindEvent(
            IConnection::EVENT_AFTER_EXECUTE_QUERY,
            function (IEvent $event) use ($self) {
                /**
                 * @var IQueryBuilder $builder
                 */
                $builder = $event->getParam('queryBuilder');
                if ($builder) {
                    $self->queries[] = $builder->getSql();
                }
            }
        );
    }

    public function testAdd()
    {

        $this->queries = [];

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);

        $blog1 = $blogsCollection->add('test_blog');
        $blog1->setValue('title', 'test_blog');

        $post1 = $postsCollection->add('test_post', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post');

        $this->getObjectPersister()->commit();

        $expectedResult = [
            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `slug` = :slug, `child_count` = :child_count, `title` = :title',
            'INSERT INTO `umi_mock_blogs`
SET `id` = :id, `type` = :type, `guid` = :guid, `slug` = :slug, `child_count` = :child_count, `title` = :title',
            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `pid` = :pid, `slug` = :slug, `title` = :title',
            'INSERT INTO `umi_mock_posts`
SET `id` = :id, `type` = :type, `guid` = :guid, `pid` = :pid, `slug` = :slug, `title` = :title',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` IS :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_blogs`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_hierarchy`
SET `pid` = :pid, `version` = `version` + (1)
WHERE `id` = :objectId AND `version` = :version',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` = :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_posts`
SET `version` = `version` + (1), `pid` = :pid
WHERE `id` = :objectId AND `version` = :version',
            'UPDATE `umi_mock_posts`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId'
        ];

        $this->assertEquals($expectedResult, $this->queries, 'Неверные запросы при добавлении иерархических объектов');
    }

    public function testAddAndUpdate()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $blog = $blogsCollection->add('first_blog');
        $blog->setValue('title', 'first_blog');
        $this->getObjectPersister()->commit();
        $this->queries = [];

        $postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);
        $post = $postsCollection->add('test_post', IObjectType::BASE, $blog);
        $post->setValue('title', 'test_post');

        $this->getObjectPersister()->commit();

        $expectedResult = [

            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `pid` = :pid, `slug` = :slug, `title` = :title',
            'INSERT INTO `umi_mock_posts`
SET `id` = :id, `type` = :type, `guid` = :guid, `pid` = :pid, `slug` = :slug, `title` = :title',
            'UPDATE `umi_mock_hierarchy`
SET `child_count` = `child_count` + (1)
WHERE `id` = :objectId',
            'UPDATE `umi_mock_blogs`
SET `child_count` = `child_count` + (1)
WHERE `id` = :objectId',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` = :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_posts`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->queries,
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
        $this->queries = [];

        $blog = $blogsCollection->get($blog1Guid);
        $blog->setValue('title', 'new_title');

        $this->getObjectPersister()->commit();

        $expectedResult = [
            'UPDATE `umi_mock_hierarchy`
SET `title` = :title, `version` = `version` + (1)
WHERE `id` = :objectId AND `version` = :version',
            'UPDATE `umi_mock_blogs`
SET `version` = `version` + (1), `title` = :title
WHERE `id` = :objectId AND `version` = :version'
        ];

        $this->assertEquals($expectedResult, $this->queries, 'Неверные запросы при изменении иерархических объектов');

        $this->queries = [];
        $blog->setValue('publishTime', '13.08.13');
        $this->getObjectPersister()->commit();
        $expectedResult = [
            'UPDATE `umi_mock_blogs`
SET `publish_time` = :publish_time, `version` = `version` + (1)
WHERE `id` = :objectId AND `version` = :version'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->queries,
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
        $this->queries = [];

        $blog = $blogsCollection->get($blog1Guid);
        $blogsCollection->delete($blog);
        $this->getObjectPersister()->commit();

        $expectedResult = [
            'DELETE FROM `umi_mock_hierarchy`
WHERE `id` = :objectId',
            'DELETE FROM `umi_mock_blogs`
WHERE `id` = :objectId'
        ];

        $this->assertEquals($expectedResult, $this->queries, 'Неверные запросы при удалении иерархических объектов');
    }

}

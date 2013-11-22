<?php
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\orm\collection\ICommonHierarchy;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

class CommonHierarchyTest extends ORMDbTestCase
{
    /**
     * @var Connection $Connection
     */
    protected $connection;

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

    /**
     * @return array
     */
    final protected function getQueries()
    {
        return array_values(
            array_map(
                function ($a) {
                    return $a['sql'];
                },
                $this->sqlLogger()->queries
            )
        );
    }

    /**
     * @param array $queries
     */
    public function setQueries($queries)
    {
        $this->sqlLogger()->queries = $queries;
    }

    /**
     * @return DebugStack
     */
    public function sqlLogger()
    {
        return $this->connection
            ->getConfiguration()
            ->getSQLLogger();
    }

    protected function setUpFixtures()
    {
        $this->connection = $this->getDbCluster()->getConnection();
        $this->connection
            ->getConfiguration()
            ->setSQLLogger(new DebugStack());
    }

    public function testHierarchy()
    {
        /**
         * @var ICommonHierarchy $hierarchy
         */
        $hierarchy = $this->collectionManager->getCollection(self::SYSTEM_HIERARCHY);

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->collectionManager->getCollection(self::BLOGS_POST);

        $blog1 = $hierarchy->add($blogsCollection, 'test_blog1');
        $blog1->setValue('title', 'test_blog1');

        $blog2 = $hierarchy->add($blogsCollection, 'test_blog2');
        $blog2->setValue('title', 'test_blog2');

        $post1 = $hierarchy->add($postsCollection, 'test_post1', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post1');

        $post2 = $hierarchy->add($postsCollection, 'test_post2', IObjectType::BASE, $blog1);
        $post2->setValue('title', 'test_post2');

        $post3 = $hierarchy->add($postsCollection, 'test_post3', IObjectType::BASE, $post1);
        $post3->setValue('title', 'test_post3');

        $this->assertEquals(
            self::BLOGS_BLOG,
            $blog1->getCollectionName(),
            'Ожидается, что добавление объекта в общую иерархию добавит объект в указанную связанную коллекцию'
        );
        $this->assertEquals(
            self::BLOGS_POST,
            $post1->getCollectionName(),
            'Ожидается, что добавление объекта в общую иерархию добавит объект в указанную связанную коллекцию'
        );

        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();

        $this->setQueries([]);

        $set = $hierarchy
            ->select()
            ->fields([IObject::FIELD_IDENTIFY])
            ->where(IHierarchicObject::FIELD_ORDER)
            ->equals(2)
            ->result();

        /**
         * @var IHierarchicObject $blog
         */
        $blog = $set->fetch();
        $post = $set->fetch();

        $queries = [
            'SELECT "system_hierarchy"."id" AS "system_hierarchy:id", '
            . '"system_hierarchy"."guid" AS "system_hierarchy:guid", "system_hierarchy"."type" AS "system_hierarchy:type",'
            .' "system_hierarchy"."version" AS "system_hierarchy:version",'
            .' "system_hierarchy"."pid" AS "system_hierarchy:parent", "system_hierarchy"."mpath" AS "system_hierarchy:mpath",'
            .' "system_hierarchy"."slug" AS "system_hierarchy:slug", "system_hierarchy"."uri" AS "system_hierarchy:uri"
FROM "umi_mock_hierarchy" AS "system_hierarchy"
WHERE (("system_hierarchy"."order" = :value0))'
        ];

        $this->assertEquals($queries, $this->getQueries(), 'Ожидается выборка только по таблицы иерархии');

        $this->assertEquals(
            'blogs_blog',
            $blog->getCollection()
                ->getName(),
            'Ожидается, что первый элемент на втором месте это блог'
        );
        $this->assertEquals(2, $blog->getId(), 'Ожидается, что первый элемент на втором месте имеет id 2');

        $this->assertEquals(
            'blogs_post',
            $post->getCollection()
                ->getName(),
            'Ожидается, что второй элемент на втором месте это пост'
        );
        $this->assertEquals(4, $post->getId(), 'Ожидается, что второй элемент на втором месте имеет id 4');

        $this->setQueries([]);
        $blog->getValue('title');

        $queries = [
            'SELECT "blogs_blog"."id" AS "blogs_blog:id", "blogs_blog"."guid" AS "blogs_blog:guid", "blogs_blog"."type" AS "blogs_blog:type", "blogs_blog"."version" AS "blogs_blog:version", "blogs_blog"."pid" AS "blogs_blog:parent", "blogs_blog"."mpath" AS "blogs_blog:mpath", "blogs_blog"."slug" AS "blogs_blog:slug", "blogs_blog"."uri" AS "blogs_blog:uri", "blogs_blog"."child_count" AS "blogs_blog:childCount", "blogs_blog"."order" AS "blogs_blog:order", "blogs_blog"."level" AS "blogs_blog:level", "blogs_blog"."title" AS "blogs_blog:title#ru-RU", "blogs_blog"."title_en" AS "blogs_blog:title#en-US", "blogs_blog"."publish_time" AS "blogs_blog:publishTime", "blogs_blog"."owner_id" AS "blogs_blog:owner"
FROM "umi_mock_blogs" AS "blogs_blog"
WHERE (("blogs_blog"."id" = :value0))'
        ];

        $this->assertEquals(
            $queries,
            $this->getQueries(),
            'Ожидается, что после запроса свойства объекта он будет дозагружен из неиерархической таблицы'
        );

        $post3 = $postsCollection->getById(5);
        /**
         * @var IHierarchicObject $parent1
         * @var IHierarchicObject $parent2
         */
        list($parent1, $parent2) = $hierarchy->selectAncestry($post3)
            ->result()
            ->fetchAll();

        $this->assertEquals(1, $parent1->getId(), 'Ожидается, что первый предок третьего поста blog1');
        $this->assertEquals(3, $parent2->getId(), 'Ожидается, что второй предок третьего поста post1');

        $this->assertEmpty(
            $hierarchy->selectAncestry($blog)
                ->result()
                ->fetchAll(),
            'Ожидается, что у blog1 нет родителей'
        );

    }

}

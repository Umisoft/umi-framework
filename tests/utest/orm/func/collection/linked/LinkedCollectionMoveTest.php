<?php

use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use utest\orm\ORMDbTestCase;

/**
 * Тест перемещения по связанной иерархической коллекции
 */
class LinkedCollectionMoveTest extends ORMDbTestCase
{

    protected $usedDbServerId = 'mysqlMaster';

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
     * @var IHierarchicObject $blog1
     */
    protected $blog1;
    /**
     * @var IHierarchicObject $blog2
     */
    protected $blog2;
    /**
     * @var IHierarchicObject $post2
     */
    protected $post2;
    /**
     * @var IHierarchicObject $post3
     */
    protected $post3;

    protected function setUpFixtures()
    {

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $postsCollection = $this->collectionManager->getCollection(self::BLOGS_POST);

        $this->blog1 = $blogsCollection->add('blog1');
        $this->blog1->setValue('title', 'blog');

        $this->blog2 = $blogsCollection->add('blog2', IObjectType::BASE, $this->blog1);
        $this->blog2->setValue('title', 'blog2');

        $post1 = $postsCollection->add('post1', IObjectType::BASE, $this->blog1);
        $post1->setValue('title', 'post1');

        $blog3 = $blogsCollection->add('blog3', IObjectType::BASE, $this->blog1);
        $blog3->setValue('title', 'blog3');

        $this->post2 = $postsCollection->add('post2', IObjectType::BASE, $blog3);
        $this->post2->setValue('title', 'post2');

        $this->post3 = $postsCollection->add('post3', IObjectType::BASE, $this->post2);
        $this->post3->setValue('title', 'post3');

        $this->objectPersister->commit();

    }

    public function testMoveAfterWithSwitchingBranch()
    {

        $this->collectionManager->getCollection(self::BLOGS_BLOG)
            ->move($this->blog2, $this->post2, $this->post3);

        $this->assertEquals(
            2,
            $this->blog2->getOrder(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            5,
            $this->blog2->getParent()
                ->getId(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            '#1.4.5.2',
            $this->blog2->getMaterializedPath(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            3,
            $this->blog2->getLevel(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            4,
            $this->blog2->getVersion(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            '//blog1/blog3/post2/blog2',
            $this->blog2->getURI(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );

        $this->assertEquals(
            2,
            $this->blog1->getChildCount(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );
        $this->assertEquals(
            2,
            $this->post2->getChildCount(),
            'Ожидается, что перемещение можно выполнять в связанной иерархической коллекции объекта'
        );

    }

}

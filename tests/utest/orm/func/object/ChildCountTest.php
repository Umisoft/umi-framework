<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\orm\collection\ILinkedHierarchicCollection;
use umi\orm\metadata\IObjectType;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для вычисления количества детей у иерархических объектов
 */
class ChildCountTest extends ORMDbTestCase
{

    /**
     * @var ILinkedHierarchicCollection $blogsCollection
     */
    protected $blogsCollection;
    /**
     * @var ILinkedHierarchicCollection $postsCollection
     */
    protected $postsCollection;

    protected $guid1;
    protected $guid2;
    protected $guid3;
    protected $guid4;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::USERS_GROUP,
            self::USERS_USER,
            self::SYSTEM_HIERARCHY,
            self::BLOGS_BLOG,
            self::BLOGS_POST,
        ];
    }

    protected function setUpFixtures()
    {

        $this->blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);
        $this->postsCollection = $this->collectionManager->getCollection(self::BLOGS_POST);

        $blog1 = $this->blogsCollection->add('test_blog');
        $blog1->setValue('title', 'test_blog');
        $this->guid4 = $blog1->getGUID();

        $post1 = $this->postsCollection->add('test_post', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post');
        $this->guid1 = $post1->getGUID();

        $post2 = $this->postsCollection->add('test_post2', IObjectType::BASE, $blog1);
        $post2->setValue('title', 'test_post2');
        $this->guid2 = $post2->getGUID();

        $post3 = $this->postsCollection->add('test_post3', IObjectType::BASE, $post1);
        $post3->setValue('title', 'test_post3');
        $this->guid3 = $post3->getGUID();

        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();
    }

    public function testChildCount()
    {

        $post3 = $this->postsCollection->get($this->guid3);
        $blog1 = $this->blogsCollection->get($this->guid4);
        $post1 = $this->postsCollection->get($this->guid1);

        $this->assertEquals(0, $post3->getChildCount());
        $this->assertEquals(1, $post1->getChildCount());
        $this->assertEquals(2, $blog1->getChildCount());
    }

    public function testDeleteChildCount()
    {

        $post3 = $this->postsCollection->get($this->guid3);
        $this->postsCollection->delete($post3);
        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();

        $post1 = $this->postsCollection->get($this->guid1);

        $this->assertEquals(0, $post1->getChildCount());
    }
}

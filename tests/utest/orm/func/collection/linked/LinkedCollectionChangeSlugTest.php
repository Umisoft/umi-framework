<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\linked;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ILinkedHierarchicCollection;
use umi\orm\metadata\IObjectType;
use utest\orm\ORMDbTestCase;

class LinkedCollectionChangeSlugTest extends ORMDbTestCase
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
                ],
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_GROUP            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    protected $postGuid;
    protected $blogGuid;

    /**
     * @var ILinkedHierarchicCollection $blogsCollection
     */
    protected $blogsCollection;
    /**
     * @var ILinkedHierarchicCollection $postsCollection
     */
    protected $postsCollection;

    protected function setUpFixtures()
    {
        $this->blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $this->postsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_POST);

        $blog1 = $this->blogsCollection->add('blog1');
        $blog1->setValue('title', 'test_blog');
        $this->blogGuid = $blog1->getGUID();

        $post1 = $this->postsCollection->add('post1', IObjectType::BASE, $blog1);
        $post1->setValue('title', 'test_post');

        $post2 = $this->postsCollection->add('post2', IObjectType::BASE, $post1);
        $post2->setValue('title', 'test_post2');
        $this->postGuid = $post2->getGUID();

        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

    }

    public function testChangeSlug()
    {

        $blog = $this->blogsCollection->get($this->blogGuid);
        $this->blogsCollection->changeSlug($blog, 'new_slug');

        $blog = $this->blogsCollection->get($this->blogGuid);
        $post = $this->postsCollection->get($this->postGuid);

        $this->assertEquals(
            '//new_slug',
            $blog->getURI(),
            'Ожидается, что изменение последней части ЧПУ объекта можно выполнить у его коллекции'
        );
        $this->assertEquals(
            '//new_slug/post1/post2',
            $post->getURI(),
            'Ожидается, что изменение последней части ЧПУ объекта у его коллекции'
            .' затронет и его детей из других коллекций'
        );
    }

}

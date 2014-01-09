<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\orm\collection\ICollectionFactory;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

/**
 * Тесты свойств объекта
 */
class ObjectPropertiesTest extends ORMDbTestCase
{
    /**
     * @var string $blogGuid
     */
    protected $blogGuid;
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

    protected function setUpFixtures()
    {
        $blog = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG)
            ->add('test')
            ->setValue('title', 'testRussianTitle')
            ->setValue('title', 'testEnglishTitle', 'en-US')
            ->setValue('publishTime', '12-12-2012');

        $this->getObjectPersister()->commit();
        $this->blogGuid = $blog->getGUID();

        $blog->unload();
    }

    public function testLoadedProperties()
    {
        $blog = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG)
            ->select()
            ->fields(['title'])
            ->withLocalization(true)
            ->where('guid')->equals($this->blogGuid)
            ->result()
            ->fetch();

        $loadedProperties = $blog->getLoadedProperties();

        $this->assertEquals(
            [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                IHierarchicObject::FIELD_PARENT,
                IHierarchicObject::FIELD_MPATH,
                IHierarchicObject::FIELD_SLUG,
                IHierarchicObject::FIELD_URI,
                'title#ru-RU',
                'title#en-US',
                'title#en-GB',
                'title#ru-UA'
            ],
            array_keys($loadedProperties)
        );

        foreach($loadedProperties as $property) {
            $this->assertInstanceOf(
                'umi\orm\object\property\IProperty',
                $property
            );
        }
    }
}
 
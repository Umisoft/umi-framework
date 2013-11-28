<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\dbal\builder\IQueryBuilder;
use umi\dbal\cluster\IConnection;
use umi\event\IEvent;
use umi\i18n\ILocalesService;
use umi\orm\collection\ICollectionFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тест селектора
 *
 */
class LocalizedPropertiesTest extends ORMDbTestCase
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
                ]
            ],
            true
        ];
    }


    public function testLoadLocalization()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blogGuid = $blog->getGUID();
        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();
        $this->resetQueries();

        $blog = $blogsCollection->get($blogGuid);

        $this->assertEquals(
            [
                'SELECT "blogs_blog"."id" AS "blogs_blog:id", "blogs_blog"."guid" AS "blogs_blog:guid", "blogs_blog"."type" AS "blogs_blog:type", "blogs_blog"."version" AS "blogs_blog:version", "blogs_blog"."pid" AS "blogs_blog:parent", "blogs_blog"."mpath" AS "blogs_blog:mpath", "blogs_blog"."slug" AS "blogs_blog:slug", "blogs_blog"."uri" AS "blogs_blog:uri", "blogs_blog"."child_count" AS "blogs_blog:childCount", "blogs_blog"."order" AS "blogs_blog:order", "blogs_blog"."level" AS "blogs_blog:level", "blogs_blog"."title" AS "blogs_blog:title#ru-RU", "blogs_blog"."title_en" AS "blogs_blog:title#en-US", "blogs_blog"."publish_time" AS "blogs_blog:publishTime", "blogs_blog"."owner_id" AS "blogs_blog:owner"
FROM "umi_mock_blogs" AS "blogs_blog"
WHERE (("blogs_blog"."guid" = :value0))'
            ],
            $this->getQueries(),
            'Ожидается, что при получении объекта в запросе участвуют только текущая и дефолтная локали'
        );

        $this->resetQueries();
        $blog->getValue('title', 'ru-RU');
        $blog->getValue('title', 'en-US');
        $this->assertEmpty(
            $this->getQueries(),
            'Ожидается, что при получении объекта значения для текущей локали и дефолтной локали уже подгружены'
        );

        $blog->getValue('title', 'en-GB');

        $this->assertEquals(
            [
                'SELECT "blogs_blog"."id" AS "blogs_blog:id", "blogs_blog"."guid" AS "blogs_blog:guid", "blogs_blog"."type" AS "blogs_blog:type", "blogs_blog"."version" AS "blogs_blog:version", "blogs_blog"."pid" AS "blogs_blog:parent", "blogs_blog"."mpath" AS "blogs_blog:mpath", "blogs_blog"."slug" AS "blogs_blog:slug", "blogs_blog"."uri" AS "blogs_blog:uri", "blogs_blog"."title" AS "blogs_blog:title#ru-RU", "blogs_blog"."title_en" AS "blogs_blog:title#en-US", "blogs_blog"."title_gb" AS "blogs_blog:title#en-GB", "blogs_blog"."title_ua" AS "blogs_blog:title#ru-UA"
FROM "umi_mock_blogs" AS "blogs_blog"
WHERE (("blogs_blog"."id" = :value0))'
            ],
            $this->getQueries(),
            'Ожидается, что при запросе значения для не текущей и не дефолтной локали будут подгружены все локализации объекта'
        );
    }

    public function testEqualLocalesQuery()
    {
        /**
         * @var ILocalesService $locales
         */
        $locales = $this->getTestToolkit()
            ->getService('umi\i18n\ILocalesService');
        $locales->setDefaultLocale('en-US');
        $locales->setCurrentLocale('en-US');

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current russian title', 'ru-RU');
        $blogGuid = $blog->getGUID();
        $this->getObjectPersister()->commit();

        $this->getObjectManager()->unloadObjects();
        $this->resetQueries();

        $blog = $blogsCollection->get($blogGuid);

        $expectedResult = [
            'SELECT "blogs_blog"."id" AS "blogs_blog:id", "blogs_blog"."guid" AS "blogs_blog:guid", "blogs_blog"."type" AS "blogs_blog:type", "blogs_blog"."version" AS "blogs_blog:version", "blogs_blog"."pid" AS "blogs_blog:parent", "blogs_blog"."mpath" AS "blogs_blog:mpath", "blogs_blog"."slug" AS "blogs_blog:slug", "blogs_blog"."uri" AS "blogs_blog:uri", "blogs_blog"."child_count" AS "blogs_blog:childCount", "blogs_blog"."order" AS "blogs_blog:order", "blogs_blog"."level" AS "blogs_blog:level", "blogs_blog"."title_en" AS "blogs_blog:title#en-US", "blogs_blog"."publish_time" AS "blogs_blog:publishTime", "blogs_blog"."owner_id" AS "blogs_blog:owner"
FROM "umi_mock_blogs" AS "blogs_blog"
WHERE (("blogs_blog"."guid" = :value0))'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Ожидается, что, если дефолтная и текущая локаль совпадают, то в запросе присутсвует только одна локаль.'
        );
        $this->assertNull($blog->getValue('title'), 'Ожидается, что для текущей локали значение null');
    }

    public function testCurrentLocaleProperties()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current russian title');
        $blogGuid = $blog->getGUID();
        $this->getObjectPersister()->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "slug", "title" ) VALUES ( :type, :guid, :slug, :title )',
            'INSERT INTO "umi_mock_blogs"
( "id", "type", "guid", "slug", "title" ) VALUES ( :id, :type, :guid, :slug, :title )',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" IS :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_blogs"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта без указания локали'
        );
        $this->resetQueries();
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($blogGuid);
        $this->assertEquals(
            'current russian title',
            $blog->getValue('title'),
            'Ожидается, что при запросе свойства без локали вернется значение текущей локали'
        );

        $expectedResult = [
            'SELECT "blogs_blog"."id" AS "blogs_blog:id", "blogs_blog"."guid" AS "blogs_blog:guid", "blogs_blog"."type" AS "blogs_blog:type", "blogs_blog"."version" AS "blogs_blog:version", "blogs_blog"."pid" AS "blogs_blog:parent", "blogs_blog"."mpath" AS "blogs_blog:mpath", "blogs_blog"."slug" AS "blogs_blog:slug", "blogs_blog"."uri" AS "blogs_blog:uri", "blogs_blog"."child_count" AS "blogs_blog:childCount", "blogs_blog"."order" AS "blogs_blog:order", "blogs_blog"."level" AS "blogs_blog:level", "blogs_blog"."title" AS "blogs_blog:title#ru-RU", "blogs_blog"."title_en" AS "blogs_blog:title#en-US", "blogs_blog"."publish_time" AS "blogs_blog:publishTime", "blogs_blog"."owner_id" AS "blogs_blog:owner"
FROM "umi_mock_blogs" AS "blogs_blog"
WHERE (("blogs_blog"."guid" = :value0))'
        ];
        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Ожидается, что будут выполнены запросы на получение только свойств текущей локали c учетом значений дефолтной локали'
        );
        $this->resetQueries();

        $this->assertNull(
            $blog->getValue('title', 'en-US'),
            'Ожидается, что в дефолтной локали нет значения для свойства title'
        );
    }

    public function testDefaultLocaleProperties()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'default english title', 'en-US');
        $blogGuid = $blog->getGUID();
        $this->getObjectPersister()->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "slug", "title_en" ) VALUES ( :type, :guid, :slug, :title_en )',
            'INSERT INTO "umi_mock_blogs"
( "id", "type", "guid", "slug", "title_en" ) VALUES ( :id, :type, :guid, :slug, :title_en )',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" IS :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_blogs"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта в конкретной локали'
        );
        $this->resetQueries();
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($blogGuid);
        $this->assertEquals(
            'default english title',
            $blog->getValue('title'),
            'Ожидается, что при запросе свойства без локали вернется значение дефолтной локали, если в текущей его нет'
        );
        $this->assertEquals(
            'default english title',
            $blog->getValue('title', 'en-US'),
            'Ожидается, что при запросе свойства c локалью, если оно есть, оно и вернется'
        );
    }

    public function testAddLocalesProperties()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'russian current title');
        $blog->setValue('title', 'english default title', 'en-US');
        $blogGuid = $blog->getGUID();
        $this->getObjectPersister()->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'INSERT INTO "umi_mock_hierarchy"
( "type", "guid", "slug", "title", "title_en" ) VALUES ( :type, :guid, :slug, :title, :title_en )',
            'INSERT INTO "umi_mock_blogs"
( "id", "type", "guid", "slug", "title", "title_en" ) VALUES ( :id, :type, :guid, :slug, :title, :title_en )',
            'SELECT MAX("order") AS "order"
FROM "umi_mock_hierarchy"
WHERE "pid" IS :parent',
            'UPDATE "umi_mock_hierarchy"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            'UPDATE "umi_mock_blogs"
SET "mpath" = :mpath, "uri" = :uri, "order" = :order, "level" = :level
WHERE "id" = :objectId',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта c указанием локалей'
        );
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($blogGuid);
        $this->assertEquals(
            'russian current title',
            $blog->getValue('title'),
            'Ожидается, что вернется сохраненное значение для текущей локали'
        );
        $this->assertEquals(
            'english default title',
            $blog->getValue('title', 'en-US'),
            'Ожидается, что вернется сохраненное значение для указанной локали'
        );
    }

    public function testModifyLocalesProperties()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $this->getObjectPersister()->commit();
        $guid = $blog->getGUID();
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($guid);
        $this->resetQueries();

        $blog->setValue('title', 'russian title', 'ru-RU');
        $blog->setValue('title', 'russian current title');
        $blog->setValue('title', 'english default title', 'en-US');

        $this->getObjectPersister()->commit();

        $expectedResult = [
            '"START TRANSACTION"',
            'UPDATE "umi_mock_hierarchy"
SET "title" = :title, "title_en" = :title_en, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            'UPDATE "umi_mock_blogs"
SET "title" = :title, "title_en" = :title_en, "version" = "version" + (1)
WHERE "id" = :objectId AND "version" = :version',
            '"COMMIT"',
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на модификацию объекта c указанием локалей'
        );
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($guid);
        $this->assertEquals(
            'russian current title',
            $blog->getValue('title'),
            'Ожидается, что для объекта будет сохранено последнее выставленное значение для текущей локали вне зависимости от того, была ли указана локаль при установки значения'
        );
        $this->assertEquals(
            'english default title',
            $blog->getValue('title', 'en-US'),
            'Ожидается, что вернется сохраненное значение для указанной локали'
        );
    }

    public function testModifyCalculatedProperty()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current title', 'en-US');
        $this->getObjectPersister()->commit();
        $guid = $blog->getGUID();
        $this->getObjectManager()->unloadObjects();

        $blog = $blogsCollection->get($guid);
        $this->assertEquals(
            'current title',
            $blog->getValue('title'),
            'Ожидается, что если значения для текущей локали нет, то для при запросе свойства без локали вернется значение дефолтной локали'
        );
        $this->assertEquals(
            'current title',
            $blog->getValue('title', 'en-US'),
            'Неверное значение для конкретной локали'
        );
        $this->assertNull($blog->getValue('title', 'ru-RU'), 'Неверное значение для конкретной локали');

        $blog->setValue('title', 'english title', 'en-US');
        $blog->setValue('title', 'current title');
        $this->getObjectPersister()->commit();

        $blog = $blogsCollection->get($guid);
        $this->assertEquals(
            'english title',
            $blog->getValue('title', 'en-US'),
            'Неверное значение для конкретной локали'
        );
        $this->assertEquals(
            'current title',
            $blog->getValue('title'),
            'Ожидается, что значение для текущей локали было сохранено в базу'
        );
        $this->assertEquals(
            'current title',
            $blog->getValue('title', 'ru-RU'),
            'Ожидается, что значение для текущей локали было сохранено в базу'
        );
    }

    public function testCurrentLocaleSetValue()
    {
        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current title', 'ru-RU');

        $this->assertEquals('current title', $blog->getValue('title', 'ru-RU'));
        $this->assertEquals(
            'current title',
            $blog->getValue('title'),
            'Ожидается, что значение без локали будет равно выставленному значению для текущей локали с указанием локали'
        );
    }
}

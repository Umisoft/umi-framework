<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\i18n\ILocalesService;
use utest\orm\ORMDbTestCase;

/**
 * Тест селектора
 *
 */
class LocalizedPropertiesTest extends ORMDbTestCase
{

    /**
     * @var Connection $connection
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
        );
    }

    /**
     * @return array
     */
    protected function getQueries()
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
        $this->connection = $this
            ->getDbCluster()
            ->getConnection();
        $this->connection
            ->getConfiguration()
            ->setSQLLogger(new DebugStack());
    }

    public function testLoadLocalization()
    {

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blogGuid = $blog->getGUID();
        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();
        $this->setQueries([]);

        $blog = $blogsCollection->get($blogGuid);

        $this->assertEquals(
            [
                'SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`, `blogs_blog`.`child_count` AS `blogs_blog:childCount`, `blogs_blog`.`order` AS `blogs_blog:order`, `blogs_blog`.`level` AS `blogs_blog:level`, `blogs_blog`.`title` AS `blogs_blog:title#ru-RU`, `blogs_blog`.`title_en` AS `blogs_blog:title#en-US`, `blogs_blog`.`publish_time` AS `blogs_blog:publishTime`, `blogs_blog`.`owner_id` AS `blogs_blog:owner`
FROM `umi_mock_blogs` AS `blogs_blog`
WHERE ((`blogs_blog`.`guid` = :value0))'
            ],
            $this->getQueries(),
            'Ожидается, что при получении объекта в запросе участвуют только текущая и дефолтная локали'
        );

        $this->setQueries([]);
        $blog->getValue('title', 'ru-RU');
        $blog->getValue('title', 'en-US');
        $this->assertEmpty(
            $this->getQueries(),
            'Ожидается, что при получении объекта значения для текущей локали и дефолтной локали уже подгружены'
        );

        $blog->getValue('title', 'en-GB');

        $this->assertEquals(
            [
                'SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`, `blogs_blog`.`title` AS `blogs_blog:title#ru-RU`, `blogs_blog`.`title_en` AS `blogs_blog:title#en-US`, `blogs_blog`.`title_gb` AS `blogs_blog:title#en-GB`, `blogs_blog`.`title_ua` AS `blogs_blog:title#ru-UA`
FROM `umi_mock_blogs` AS `blogs_blog`
WHERE ((`blogs_blog`.`id` = :value0))'
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

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current russian title', 'ru-RU');
        $blogGuid = $blog->getGUID();
        $this->objectPersister->commit();

        $this->objectManager->unloadObjects();
        $this->setQueries([]);

        $blog = $blogsCollection->get($blogGuid);

        $expectedResult = [
            'SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`, `blogs_blog`.`child_count` AS `blogs_blog:childCount`, `blogs_blog`.`order` AS `blogs_blog:order`, `blogs_blog`.`level` AS `blogs_blog:level`, `blogs_blog`.`title_en` AS `blogs_blog:title#en-US`, `blogs_blog`.`publish_time` AS `blogs_blog:publishTime`, `blogs_blog`.`owner_id` AS `blogs_blog:owner`
FROM `umi_mock_blogs` AS `blogs_blog`
WHERE ((`blogs_blog`.`guid` = :value0))'
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
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current russian title');
        $blogGuid = $blog->getGUID();
        $this->objectPersister->commit();

        $expectedResult = [
            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `slug` = :slug, `title` = :title',
            'INSERT INTO `umi_mock_blogs`
SET `id` = :id, `type` = :type, `guid` = :guid, `slug` = :slug, `title` = :title',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` IS :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_blogs`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта без указания локали'
        );
        $this->setQueries([]);
        $this->objectManager->unloadObjects();

        $blog = $blogsCollection->get($blogGuid);
        $this->assertEquals(
            'current russian title',
            $blog->getValue('title'),
            'Ожидается, что при запросе свойства без локали вернется значение текущей локали'
        );

        $expectedResult = [
            'SELECT `blogs_blog`.`id` AS `blogs_blog:id`, `blogs_blog`.`guid` AS `blogs_blog:guid`, `blogs_blog`.`type` AS `blogs_blog:type`, `blogs_blog`.`version` AS `blogs_blog:version`, `blogs_blog`.`pid` AS `blogs_blog:parent`, `blogs_blog`.`mpath` AS `blogs_blog:mpath`, `blogs_blog`.`slug` AS `blogs_blog:slug`, `blogs_blog`.`uri` AS `blogs_blog:uri`, `blogs_blog`.`child_count` AS `blogs_blog:childCount`, `blogs_blog`.`order` AS `blogs_blog:order`, `blogs_blog`.`level` AS `blogs_blog:level`, `blogs_blog`.`title` AS `blogs_blog:title#ru-RU`, `blogs_blog`.`title_en` AS `blogs_blog:title#en-US`, `blogs_blog`.`publish_time` AS `blogs_blog:publishTime`, `blogs_blog`.`owner_id` AS `blogs_blog:owner`
FROM `umi_mock_blogs` AS `blogs_blog`
WHERE ((`blogs_blog`.`guid` = :value0))'
        ];
        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Ожидается, что будут выполнены запросы на получение только свойств текущей локали c учетом значений дефолтной локали'
        );
        $this->setQueries([]);

        $this->assertNull(
            $blog->getValue('title', 'en-US'),
            'Ожидается, что в дефолтной локали нет значения для свойства title'
        );
    }

    public function testDefaultLocaleProperties()
    {
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'default english title', 'en-US');
        $blogGuid = $blog->getGUID();
        $this->objectPersister->commit();

        $expectedResult = [
            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `slug` = :slug, `title_en` = :title_en',
            'INSERT INTO `umi_mock_blogs`
SET `id` = :id, `type` = :type, `guid` = :guid, `slug` = :slug, `title_en` = :title_en',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` IS :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_blogs`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта в конкретной локали'
        );
        $this->setQueries([]);
        $this->objectManager->unloadObjects();

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
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'russian current title');
        $blog->setValue('title', 'english default title', 'en-US');
        $blogGuid = $blog->getGUID();
        $this->objectPersister->commit();

        $expectedResult = [
            'INSERT INTO `umi_mock_hierarchy`
SET `type` = :type, `guid` = :guid, `slug` = :slug, `title` = :title, `title_en` = :title_en',
            'INSERT INTO `umi_mock_blogs`
SET `id` = :id, `type` = :type, `guid` = :guid, `slug` = :slug, `title` = :title, `title_en` = :title_en',
            'SELECT MAX(`order`) AS `order`
FROM `umi_mock_hierarchy`
WHERE `pid` IS :parent',
            'UPDATE `umi_mock_hierarchy`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId',
            'UPDATE `umi_mock_blogs`
SET `mpath` = :mpath, `uri` = :uri, `order` = :order, `level` = :level
WHERE `id` = :objectId'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на добавления объекта c указанием локалей'
        );
        $this->objectManager->unloadObjects();

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
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $this->objectPersister->commit();
        $guid = $blog->getGUID();
        $this->objectManager->unloadObjects();

        $blog = $blogsCollection->get($guid);
        $this->setQueries([]);

        $blog->setValue('title', 'russian title', 'ru-RU');
        $blog->setValue('title', 'russian current title');
        $blog->setValue('title', 'english default title', 'en-US');

        $this->objectPersister->commit();

        $expectedResult = [
            'UPDATE `umi_mock_hierarchy`
SET `title` = :title, `title_en` = :title_en, `version` = `version` + (1)
WHERE `id` = :objectId AND `version` = :version',
            'UPDATE `umi_mock_blogs`
SET `title` = :title, `title_en` = :title_en, `version` = `version` + (1)
WHERE `id` = :objectId AND `version` = :version'
        ];

        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы на модификацию объекта c указанием локалей'
        );
        $this->objectManager->unloadObjects();

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

        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

        $blog = $blogsCollection->add('blog');
        $blog->setValue('title', 'current title', 'en-US');
        $this->objectPersister->commit();
        $guid = $blog->getGUID();
        $this->objectManager->unloadObjects();

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
        $this->objectPersister->commit();

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
        $blogsCollection = $this->collectionManager->getCollection(self::BLOGS_BLOG);

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

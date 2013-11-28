<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\simplehierarchic;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\orm\collection\ISimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use utest\orm\ORMDbTestCase;

/**
 * Тест изменения последней части ЧПУ у объектов в простой иерархической коллекции
 */
class SimpleHierarchicCollectionChangeSlugTest extends ORMDbTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return array(
            self::SYSTEM_MENU
        );
    }

    protected $guid1;
    protected $guid3;
    protected $guid4;

    /**
     * @var ISimpleHierarchicCollection $menu
     */
    protected $menu;

    protected function setUpFixtures()
    {
        $this->menu = $this->collectionManager->getCollection(self::SYSTEM_MENU);

        $item1 = $this->menu->add('item1');
        $this->guid1 = $item1->getGUID();
        $item2 = $this->menu->add('item2', IObjectType::BASE, $item1);
        $item3 = $this->menu->add('item3', IObjectType::BASE, $item2);
        $this->guid3 = $item3->getGUID();
        $item4 = $this->menu->add('item4', IObjectType::BASE, $item2);
        $this->guid4 = $item4->getGUID();

        $this->objectPersister->commit();
        $this->objectManager->unloadObjects();
    }

    public function testURI()
    {
        $item1 = $this->menu->get($this->guid1);
        $item3 = $this->menu->get($this->guid3);

        $this->assertEquals('//item1/item2/item3', $item3->getURI());
        $this->assertEquals('//item1', $item1->getURI());
    }

    public function testChangeSlug()
    {

        $item1 = $this->menu->get($this->guid1);
        $this->resetQueries();
        $this->menu->changeSlug($item1, 'new_slug');
        $this->assertEquals(
            [
                '"START TRANSACTION"',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 1 AND "version" = 1) AS mainQuery',
                //проверка актуальности изменяемого объекта
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //new_slug AND "id" != 1',
                //проверка уникальности нового slug
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //new_slug AND "id" != 1) AS mainQuery',
                //обновление всей slug у всей ветки изменяемого объекта
                'UPDATE "umi_mock_menu"
SET "version" = "version" + 1, "uri" = REPLACE("uri", \'//item1\', \'//new_slug\')
WHERE "uri" like //item1/% OR "uri" = //item1',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на изменение slug в простой иерархической коллекции'
        );

        $item3 = $this->menu->get($this->guid3);
        $item4 = $this->menu->get($this->guid4);

        $this->assertEquals('//new_slug/item2/item3', $item3->getURI());
        $this->assertEquals('//new_slug/item2/item4', $item4->getURI());
        $this->assertEquals('//new_slug', $item1->getURI());
    }
}

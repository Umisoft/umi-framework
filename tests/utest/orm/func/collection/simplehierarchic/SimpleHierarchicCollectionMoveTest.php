<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\simplehierarchic;

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ISimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use utest\orm\ORMDbTestCase;

/**
 * Тест перемещения по общей иерархии
 */
class SimpleHierarchicCollectionMoveTest extends ORMDbTestCase
{

    /**
     * @var IHierarchicObject $menuItem1
     */
    protected $menuItem1;
    /**
     * @var IHierarchicObject $menuItem2
     */
    protected $menuItem2;
    /**
     * @var IHierarchicObject $menuItem3
     */
    protected $menuItem3;
    /**
     * @var IHierarchicObject $menuItem4
     */
    protected $menuItem4;
    /**
     * @var IHierarchicObject $menuItem5
     */
    protected $menuItem5;
    /**
     * @var IHierarchicObject $menuItem6
     */
    protected $menuItem6;
    /**
     * @var IHierarchicObject $menuItem7
     */
    protected $menuItem7;
    /**
     * @var IHierarchicObject $menuItem8
     */
    protected $menuItem8;
    /**
     * @var ISimpleHierarchicCollection $menu
     */
    protected $menu;

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
                ],
                self::SYSTEM_MENU            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {

        $this->menu = $this->getCollectionManager()->getCollection(self::SYSTEM_MENU);

        $this->menuItem1 = $this->menu->add('item1');
        $this->menuItem2 = $this->menu->add('item2');
        $this->menuItem3 = $this->menu->add('item3', IObjectType::BASE, $this->menuItem2);
        $this->menuItem4 = $this->menu->add('item4', IObjectType::BASE, $this->menuItem3);
        $this->menuItem5 = $this->menu->add('item5');
        $this->menuItem6 = $this->menu->add('item6', IObjectType::BASE, $this->menuItem5);
        $this->menuItem7 = $this->menu->add('item7', IObjectType::BASE, $this->menuItem6);
        $this->menuItem8 = $this->menu->add('item8', IObjectType::BASE, $this->menuItem5);

        $this->getObjectPersister()->commit();
    }

    public function testInitialHierarchyProperties()
    {

        $this->assertEquals(1, $this->menuItem1->getOrder());

        $this->assertEquals(2, $this->menuItem2->getOrder());
        $this->assertEquals('#2', $this->menuItem2->getMaterializedPath());
        $this->assertEquals(0, $this->menuItem2->getLevel());

        $this->assertEquals(1, $this->menuItem3->getOrder());

        $this->assertEquals('#2.3.4', $this->menuItem4->getMaterializedPath());
        $this->assertEquals(2, $this->menuItem4->getLevel());

        $this->assertEquals(2, $this->menuItem5->getChildCount());
        $this->assertEquals(3, $this->menuItem5->getOrder());
        $this->assertEquals(2, $this->menuItem5->getChildCount());

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals('#5.6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals(1, $this->menuItem6->getLevel());
        $this->assertEquals(2, $this->menuItem6->getVersion());
        $this->assertEquals(
            5,
            $this->menuItem6->getParent()
                ->getId()
        );
        $this->assertEquals(1, $this->menuItem6->getChildCount());

        $this->assertEquals('#5.6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7', $this->menuItem7->getURI());
        $this->assertEquals(
            6,
            $this->menuItem7->getParent()
                ->getId()
        );
        $this->assertEquals(2, $this->menuItem7->getLevel());

    }

    public function testImpossibleMove()
    {

        $blog = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG)
            ->add('blog');
        $this->getObjectPersister()->commit();

        $e = null;
        try {
            $this->menu->move($blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить не принадлежащий ей объект'
        );

        $e = null;
        try {
            $this->menu->move($this->menuItem2, $blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить объект'
            . ' под не принадлежащий ей объект'
        );

        $e = null;
        try {
            $this->menu->move($this->menuItem2, $this->menuItem5, $blog);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что в простой иерархической коллекции невозможно переместить объект'
            . ' рядом с не принадлежащим ей объектом'
        );

    }

    public function testMoveFirstWithoutSwitchingTheBranch()
    {

        $this->menu->move($this->menuItem5);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 5 AND "version" = 1',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 5 AND "version" = 1) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 1, "version" = "version" + 1
WHERE "id" = 5',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 5 AND "pid" IS NULL AND "order" >= 1',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(1, $this->menuItem5->getOrder());
        $this->assertEquals(2, $this->menuItem1->getOrder());
        $this->assertEquals(3, $this->menuItem2->getOrder());

    }

    public function testMoveAfterWithoutSwitchingTheBranch()
    {

        $this->menu->move($this->menuItem6, $this->menuItem5, $this->menuItem8);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 5 AND "version" = 1',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 5 AND "version" = 1) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 3, "version" = "version" + 1
WHERE "id" = 6',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 6 AND "pid" = 5 AND "order" >= 3',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(2, $this->menuItem8->getOrder());
        $this->assertEquals(3, $this->menuItem6->getOrder());

    }

    public function testMoveFirstWithSwitchingBranch()
    {

        $this->menu->move($this->menuItem6, $this->menuItem2);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item2/item6',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item2/item6) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 1, "version" = "version" + 1
WHERE "id" = 6',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 6 AND "pid" = 2 AND "order" >= 1',
                //изменение количества детей у старого родителя и нового
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (-1)
WHERE "id" = 5',
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (1)
WHERE "id" = 2',
                //изменение иерархических свойств перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "uri" = //item2/item6, "mpath" = #2.6, "pid" = 2, "version" = "version" + 1
WHERE "id" = 6',
                //изменения иерархических свойств детей перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "version" = "version" + 1, "mpath" = '
                .'REPLACE("mpath", \'#5.\', \'#2.\'), "uri" = REPLACE("uri", \'//item5/\', \'//item2/\')
WHERE "mpath" like #5.6.%',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals(2, $this->menuItem3->getOrder());

        $this->assertEquals(1, $this->menuItem5->getChildCount());
        $this->assertEquals(2, $this->menuItem2->getChildCount());

        $this->assertEquals(
            2,
            $this->menuItem6->getParent()
                ->getId()
        );

        $this->assertEquals('#2.6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals('//item2/item6', $this->menuItem6->getURI());

        $this->assertEquals('#2.6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item2/item6/item7', $this->menuItem7->getURI());

        $this->assertEquals(1, $this->menuItem6->getLevel());
        $this->assertEquals(2, $this->menuItem7->getLevel());

        $this->assertEquals(4, $this->menuItem6->getVersion());
        $this->assertEquals(3, $this->menuItem7->getVersion());

    }

    public function testMoveAfterWithSwitchingBranch()
    {

        $this->menu->move($this->menuItem7, $this->menuItem2, $this->menuItem3);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 7 AND "version" = 2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 7 AND "version" = 2) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item2/item7',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item2/item7) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 2, "version" = "version" + 1
WHERE "id" = 7',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 7 AND "pid" = 2 AND "order" >= 2',
                //изменение количества детей у старого родителя и нового
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (-1)
WHERE "id" = 6',
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (1)
WHERE "id" = 2',
                //изменение иерархических свойств перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "uri" = //item2/item7, "mpath" = #2.7, "pid" = 2, "level" = "level" + (-1), "version" = "version" + 1
WHERE "id" = 7',
                //изменения иерархических свойств детей перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "level" = "level" + (-1), "version" = "version" + 1, "mpath" = REPLACE("mpath", \'#5.6.\', \'#2.\'),'
                . ' "uri" = REPLACE("uri", \'//item5/item6/\', \'//item2/\')
WHERE "mpath" like #5.6.7.%',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(2, $this->menuItem7->getOrder());
        $this->assertEquals(
            2,
            $this->menuItem7->getParent()
                ->getId()
        );
        $this->assertEquals('#2.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals(1, $this->menuItem7->getLevel());
        $this->assertEquals(4, $this->menuItem7->getVersion());
        $this->assertEquals('//item2/item7', $this->menuItem7->getURI());

        $this->assertEquals(0, $this->menuItem6->getChildCount());
        $this->assertEquals(2, $this->menuItem2->getChildCount());

    }

    public function testMoveFromRoot()
    {

        $this->menu->move($this->menuItem2, $this->menuItem7);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 2 AND "version" = 1) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 7 AND "version" = 2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 7 AND "version" = 2) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item5/item6/item7/item2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item5/item6/item7/item2) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 1, "version" = "version" + 1
WHERE "id" = 2',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 2 AND "pid" = 7 AND "order" >= 1',
                //изменение количества детей у старого родителя и нового
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (1)
WHERE "id" = 7',
                //изменение иерархических свойств перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "uri" = //item5/item6/item7/item2, "mpath" = #5.6.7.2, "pid" = 7, "level" = "level" + (3), "version" = "version" + 1
WHERE "id" = 2',
                //изменения иерархических свойств детей перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "level" = "level" + (3), "version" = "version" + 1, "mpath" = REPLACE("mpath", \'#\', \'#5.6.7.\'),'
                . ' "uri" = REPLACE("uri", \'//\', \'//item5/item6/item7/\')
WHERE "mpath" like #2.%',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(1, $this->menuItem2->getOrder());
        $this->assertEquals(3, $this->menuItem2->getLevel());
        $this->assertEquals(
            7,
            $this->menuItem2->getParent()
                ->getId()
        );
        $this->assertEquals('#5.6.7.2', $this->menuItem2->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7/item2', $this->menuItem2->getURI());

        $this->assertEquals('#5.6.7.2.3.4', $this->menuItem4->getMaterializedPath());
        $this->assertEquals('//item5/item6/item7/item2/item3/item4', $this->menuItem4->getURI());
        $this->assertEquals(5, $this->menuItem4->getLevel());

    }

    public function testMoveToRoot()
    {

        $this->menu->move($this->menuItem6, null);

        $this->assertEquals(
            [
                '"START TRANSACTION"',
                //проверка возможности перемещения
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "id" = 6 AND "version" = 2) AS mainQuery',
                'SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item6',
                'SELECT count(*) FROM (SELECT "id"
FROM "umi_mock_menu"
WHERE "uri" = //item6) AS mainQuery',
                //изменение порядка у перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "order" = 1, "version" = "version" + 1
WHERE "id" = 6',
                //изменение порядка у остальных объектов
                'UPDATE "umi_mock_menu"
SET "order" = "order" + 1, "version" = "version" + 1
WHERE "id" != 6 AND "pid" IS NULL AND "order" >= 1',
                //изменение количества детей у старого родителя и нового
                'UPDATE "umi_mock_menu"
SET "child_count" = "child_count" + (-1)
WHERE "id" = 5',
                //изменение иерархических свойств перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "uri" = //item6, "mpath" = #6, "pid" = NULL, "level" = "level" + (-1), "version" = "version" + 1
WHERE "id" = 6',
                //изменения иерархических свойств детей перемещаемого объекта
                'UPDATE "umi_mock_menu"
SET "level" = "level" + (-1), "version" = "version" + 1, "mpath" = REPLACE("mpath", \'#5.\', \'#\'),'
                . ' "uri" = REPLACE("uri", \'//item5/\', \'//\')
WHERE "mpath" like #5.6.%',
                '"COMMIT"',
            ],
            $this->getQueries(true),
            'Неверные запросы на перемещение'
        );

        $this->assertEquals(1, $this->menuItem6->getOrder());
        $this->assertEquals(0, $this->menuItem6->getLevel());
        $this->assertNull($this->menuItem6->getParent());
        $this->assertEquals('#6', $this->menuItem6->getMaterializedPath());
        $this->assertEquals('//item6', $this->menuItem6->getURI());

        $this->assertEquals('#6.7', $this->menuItem7->getMaterializedPath());
        $this->assertEquals('//item6/item7', $this->menuItem7->getURI());
        $this->assertEquals(1, $this->menuItem7->getLevel());
    }
}

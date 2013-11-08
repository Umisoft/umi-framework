<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\unit\pagination\adapter;

use umi\orm\objectset\IObjectSet;
use umi\pagination\adapter\IPaginationAdapter;
use umi\pagination\adapter\SelectorPaginationAdapter;
use utest\orm\mock\collections\users\User;
use utest\orm\ORMTestCase;

/**
 * Тестирование SelectorPaginatorAdapterTest.
 */
class SelectorPaginatorAdapterTest extends ORMTestCase
{

    /**
     * @var IPaginationAdapter $adapter
     */
    protected $adapter;
    /**
     * @var User $user
     */
    protected $user;

    public function setUpFixtures()
    {
        $userCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $userCollection->add();
        $userCollection->add();
        $userCollection->add();
        $this->user = $userCollection->add();
        $userCollection->add();

        $this->objectPersister->commit();

        $selector = $userCollection->select();

        $this->adapter = new SelectorPaginationAdapter($selector);

    }

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return array(
            self::USERS_USER,
            self::USERS_GROUP
        );
    }

    public function testSelector()
    {
        $this->assertEquals($this->adapter->getTotal(), 5, 'Ожидается, что количество элементов в адаптере равно 5.');
        /**
         * @var IObjectSet $items
         */
        $items = $this->adapter->getItems(1, 3);

        $this->assertCount(1, $items, 'Ожидается, что список элементов будет сформирован верно.');
        $this->assertTrue($this->user === $items->fetch(), 'Ожидается, что список элементов будет сформирован верно.');

    }
}

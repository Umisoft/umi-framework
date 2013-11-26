<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\unit\pagination\adapter;

use umi\orm\collection\ICollectionFactory;
use umi\orm\objectset\IObjectSet;
use umi\pagination\adapter\IPaginationAdapter;
use umi\pagination\adapter\SelectorPaginationAdapter;
use utest\event\TEventSupport;
use utest\orm\mock\collections\User;
use utest\orm\TORMSetup;
use utest\orm\TORMSupport;
use utest\pagination\PaginationTestCase;

/**
 * Тестирование SelectorPaginatorAdapterTest.
 */
class SelectorPaginatorAdapterTest extends PaginationTestCase
{

    use TORMSupport;
    use TORMSetup;
    use TEventSupport;

    /**
     * @var IPaginationAdapter $adapter
     */
    protected $adapter;
    /**
     * @var User $user
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            __DIR__ . '/../../mock/collections',
            [
                'users_user'             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {

        $this->registerORMTools();
        $this->registerDbalTools();
        $this->registerEventTools();
        $this->setUpORM();

        $userCollection = $this->getCollectionManager()->getCollection('users_user');

        $userCollection->add();
        $userCollection->add();
        $userCollection->add();
        $this->user = $userCollection->add();
        $userCollection->add();

        $this->getObjectPersister()->commit();

        $selector = $userCollection->select();

        $this->adapter = new SelectorPaginationAdapter($selector);

    }

    protected function tearDown()
    {
        $this->tearDownORM();
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

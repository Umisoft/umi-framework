<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\config\entity\Config;
use umi\orm\collection\CollectionManager;
use umi\orm\collection\ICollectionFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для ObjectManager
 */
class CollectionManagerTest extends ORMDbTestCase
{

    /**
     * @var CollectionManager
     */
    protected $collectionManager;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [];
    }

    public function testConfigCollections()
    {
        $collections = [
            'users_user'    => [
                'type'  => ICollectionFactory::TYPE_SIMPLE,
                'class' => 'utest\orm\mock\collections\users\UsersCollection'
            ],
            'users_group'   => ['type' => ICollectionFactory::TYPE_SIMPLE],
            'users_profile' => ''
        ];

        $this->collectionManager->collections = new Config($collections);

        $this->assertEquals(
            ['users_user', 'users_group', 'users_profile'],
            $this->collectionManager->getList(),
            'Ожидается, что у менеджера объектов 3 коллекции'
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ICollection',
            $this->collectionManager->getCollection('users_group'),
            'Ожидается, что IObjectManager::getCollection() вернет ICollection'
        );
    }

    public function testArrayCollections()
    {

        $this->collectionManager->collections = [
            'users_user'    => [
                'type'  => ICollectionFactory::TYPE_SIMPLE,
                'class' => 'utest\orm\mock\collections\users\UsersCollection'
            ],
            'users_group'   => ['type' => ICollectionFactory::TYPE_SIMPLE],
            'users_profile' => ''
        ];

        $this->assertEquals(
            ['users_user', 'users_group', 'users_profile'],
            $this->collectionManager->getList(),
            'Ожидается, что у менеджера объектов 3 коллекции'
        );
        $this->assertTrue(
            $this->collectionManager->hasCollection('users_user'),
            'Ожидается, что коллекция users_user существует'
        );
        $this->assertFalse(
            $this->collectionManager->hasCollection('users_user_1'),
            'Ожидается, что коллекция users_user_1 не существует'
        );

        $collection = $this->collectionManager->getCollection('users_user');
        $this->assertInstanceOf(
            'umi\orm\collection\ICollection',
            $collection,
            'Ожидается, что IObjectManager::getCollection() вернет ICollection'
        );
        $this->assertTrue(
            $collection === $this->collectionManager->getCollection('users_user'),
            'Ожидается, что при повторном запросе коллекции вернется тот же самый экземпляр'
        );

        $e = null;
        try {
            $this->collectionManager->getCollection('users_user_1');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить несуществующую коллекцию'
        );
        $this->assertEquals(
            'Object collection "users_user_1" is not registered.',
            $e->getMessage(),
            'Произошло неожидаемое исключение'
        );

        $e = null;
        try {
            $this->collectionManager->getCollection('users_profile');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке получить коллекцию c неверным конфигом'
        );
        $this->assertEquals(
            'Configuration for collection "users_profile" is not valid.',
            $e->getMessage(),
            'Произошло неожидаемое исключение'
        );

    }

}

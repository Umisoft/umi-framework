<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func;

use umi\orm\collection\ISimpleCollection;
use umi\orm\object\IObject;
use utest\orm\ORMDbTestCase;

/**
 * Тесты выгрузки объектов
 */
class UnloadObjectTest extends ORMDbTestCase
{

    public $queries = [];


    protected $userGuid;
    protected $userId;
    /**
     * @var IObject $user
     */
    protected $user;

    /**
     * @var ISimpleCollection $userCollection
     */
    protected $userCollection;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::USERS_GROUP,
            self::USERS_USER,
        ];
    }

    protected function setUpFixtures()
    {
        $this->userCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $this->user = $this->userCollection->add();
        $this->objectPersister->commit();

        $this->userGuid = $this->user->getGUID();
        $this->userId = $this->user->getId();
    }

    public function testGettingStoredObjectById()
    {
        $this->userCollection->getById($this->userId);
        $this->userCollection->getById($this->userId);
        $this->assertEmpty(
            $this->queries,
            'Ожидается, что никакие запросы не будут выполнены для получения невыгруженного объекта'
        );
    }

    public function testGettingStoredObjectByGuid()
    {
        $this->userCollection->get($this->userGuid);
        $this->userCollection->get($this->userGuid);
        $this->assertEmpty(
            $this->queries,
            'Ожидается, что никакие запросы не будут выполнены для получения невыгруженного объекта'
        );
    }

    public function testGettingUnloadedObjectById()
    {
        $this->user->unload();
        $this->userCollection->getById($this->userId);
        $this->userCollection->getById($this->userId);
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что объект будет снова загружен из базы данных, если он был выгружен'
        );
    }

    public function testGettingObjectByIdAfterObjectManagerUnload()
    {
        $this->objectManager->unloadObjects();
        $this->userCollection->getById($this->userId);
        $this->userCollection->getById($this->userId);
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что объект будет снова загружен из базы данных, если менеджер объектов выгрузил объекты'
        );
    }

    public function testGettingUnloadedObjectByGuid()
    {
        $this->user->unload();
        $this->userCollection->get($this->userGuid);
        $this->userCollection->get($this->userGuid);
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что объект будет снова загружен из базы данных, если он был выгружен'
        );
    }

    public function testGettingObjectByGuidAfterObjectManagerUnload()
    {
        $this->objectManager->unloadObjects();
        $this->userCollection->get($this->userGuid);
        $this->userCollection->get($this->userGuid);
        $this->assertEquals(
            1,
            count($this->getOnlyQueries('select')),
            'Ожидается, что объект будет снова загружен из базы данных, если менеджер объектов выгрузил объекты'
        );
    }

    public function testDeletedObjectAfterManagerUnload()
    {
        $this->userCollection->delete($this->user);
        $this->assertFalse($this->objectPersister->getIsPersisted());

        $this->objectManager->unloadObjects();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

    public function testDeletedObjectAfterUnload()
    {
        $this->userCollection->delete($this->user);

        $this->user->unload();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

    public function testModifiedObjectAfterManagerUnload()
    {
        $this->user->setValue('login', 'new_login');
        $this->assertFalse($this->objectPersister->getIsPersisted());

        $this->objectManager->unloadObjects();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

    public function testModifiedObjectAfterUnload()
    {
        $this->user->setValue('login', 'new_login');
        $this->user->unload();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

    public function testAddedObjectAfterManagerUnload()
    {
        $this->userCollection->add();
        $this->assertFalse($this->objectPersister->getIsPersisted());

        $this->objectManager->unloadObjects();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

    public function testAddedObjectAfterUnload()
    {
        $user = $this->userCollection->add();
        $user->unload();
        $this->assertTrue($this->objectPersister->getIsPersisted());
    }

}

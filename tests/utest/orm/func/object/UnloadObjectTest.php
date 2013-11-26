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
use umi\orm\collection\ICollectionFactory;
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
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
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

        $this->userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $this->user = $this->userCollection->add();
        $this->getObjectPersister()->commit();

        $this->userGuid = $this->user->getGUID();
        $this->userId = $this->user->getId();

        $this->queries = [];
        $self = $this;
        $this->getDbCluster()
            ->getDbDriver()
            ->bindEvent(
            IConnection::EVENT_AFTER_EXECUTE_QUERY,
            function (IEvent $event) use ($self) {
                /**
                 * @var IQueryBuilder $builder
                 */
                $builder = $event->getParam('queryBuilder');
                if ($builder) {
                    $self->queries[] = get_class($builder);
                }
            }
        );
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
            ['umi\dbal\builder\SelectBuilder'],
            $this->queries,
            'Ожидается, что объект будет снова загружен из базы данных, если он был выгружен'
        );
    }

    public function testGettingObjectByIdAfterObjectManagerUnload()
    {
        $this->getObjectManager()->unloadObjects();
        $this->userCollection->getById($this->userId);
        $this->userCollection->getById($this->userId);
        $this->assertEquals(
            ['umi\dbal\builder\SelectBuilder'],
            $this->queries,
            'Ожидается, что объект будет снова загружен из базы данных, если менеджер объектов выгрузил объекты'
        );
    }

    public function testGettingUnloadedObjectByGuid()
    {
        $this->user->unload();
        $this->userCollection->get($this->userGuid);
        $this->userCollection->get($this->userGuid);
        $this->assertEquals(
            ['umi\dbal\builder\SelectBuilder'],
            $this->queries,
            'Ожидается, что объект будет снова загружен из базы данных, если он был выгружен'
        );
    }

    public function testGettingObjectByGuidAfterObjectManagerUnload()
    {
        $this->getObjectManager()->unloadObjects();
        $this->userCollection->get($this->userGuid);
        $this->userCollection->get($this->userGuid);
        $this->assertEquals(
            ['umi\dbal\builder\SelectBuilder'],
            $this->queries,
            'Ожидается, что объект будет снова загружен из базы данных, если менеджер объектов выгрузил объекты'
        );
    }

    public function testDeletedObjectAfterManagerUnload()
    {
        $this->userCollection->delete($this->user);
        $this->assertFalse($this->getObjectPersister()->getIsPersisted());

        $this->getObjectManager()->unloadObjects();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

    public function testDeletedObjectAfterUnload()
    {
        $this->userCollection->delete($this->user);

        $this->user->unload();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

    public function testModifiedObjectAfterManagerUnload()
    {
        $this->user->setValue('login', 'new_login');
        $this->assertFalse($this->getObjectPersister()->getIsPersisted());

        $this->getObjectManager()->unloadObjects();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

    public function testModifiedObjectAfterUnload()
    {
        $this->user->setValue('login', 'new_login');
        $this->user->unload();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

    public function testAddedObjectAfterManagerUnload()
    {
        $this->userCollection->add();
        $this->assertFalse($this->getObjectPersister()->getIsPersisted());

        $this->getObjectManager()->unloadObjects();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

    public function testAddedObjectAfterUnload()
    {
        $user = $this->userCollection->add();
        $user->unload();
        $this->assertTrue($this->getObjectPersister()->getIsPersisted());
    }

}

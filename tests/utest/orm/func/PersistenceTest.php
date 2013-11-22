<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\persister;

use umi\orm\object\IObject;
use utest\orm\mock\collections\users\User;
use utest\orm\ORMDbTestCase;

/**
 * Тесты персистентности базы данных после изменения состояния объектов
 */
class PersistenceTest extends ORMDbTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [
            self::USERS_GROUP,
            self::USERS_USER,
            self::SYSTEM_HIERARCHY,
        ];
    }

    public function testAdd()
    {
        /**
         * @var User $user
         */
        $usersCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $user = $this->objectManager->registerNewObject(
            $usersCollection,
            $usersCollection->getMetadata()
                ->getBaseType()
        );
        $user->setValue('login', 'test_login');
        $user->isActive = false;

        $this->assertTrue($user->getIsNew(), 'Ожидается, что при создании объект помечается как новый');

        $this->objectPersister->commit();
        $this->assertFalse($user->getIsNew(), 'Ожидается, что после сохранения объект помечается как не новый');

        $primaryKeyField = $usersCollection->getIdentifyField();
        $primaryKeyColumnName = $primaryKeyField->getColumnName();

        $dataSource = $user->getCollection()
            ->getMetadata()
            ->getCollectionDataSource();
        $select = $dataSource->select();
        $select->where()
            ->expr($primaryKeyColumnName, '=', ':objectId');
        $select->bindValue(':objectId', $user->getId(), $primaryKeyField->getDataType());
        $result = $select->execute();
        $values = $result->fetch();

        $this->assertSame(1, $user->getId(), 'Ожидается, что после записи в бд пользователю выставился id 1');
        $this->assertEquals(1, $values[IObject::FIELD_IDENTIFY], 'Ожидается, что в бд для пользователя записался id 1');
        $this->assertEquals(
            'test_login',
            $values['login'],
            'Ожидается, что в бд для пользователя записался login "test_login"'
        );
        $this->assertSame(
            'test_login',
            $user->getLogin(),
            'Ожидается, что после записи в бд у пользователя сохранилось вручную выставленное значение'
        );
        $this->assertSame(
            false,
            $user->getValue('isActive'),
            'Ожидается, что свойство пользователя isActive после записи изменило дефолтное значение на false'
        );
        $this->assertEquals(0, $values['is_active'], 'Ожидается, что в бд для пользователя записался isActive "0"');
        $this->assertEquals(
            'users_user.base',
            $values[IObject::FIELD_TYPE],
            'Ожидается, что для всех объектов в бд записывается полный путь до типа: имя_коллекции.имя_типа'
        );

        $this->assertFalse($user->getIsModified(), 'Ожидается, что после commit объект помечается, как неизмененный');

    }

    public function testModify()
    {
        /**
         * @var User $user
         */
        $usersCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $user = $this->objectManager->registerNewObject(
            $usersCollection,
            $usersCollection->getMetadata()
                ->getBaseType()
        );
        $user->login = 'test_login';
        $user->isActive = false;
        $user->setValue('height', 163);
        $this->objectPersister->commit();

        $oldPassword = $user->getPassword();
        $oldHeight = $user->getValue('height');
        $oldRating = $user->getValue('rating');

        $user->login = 'new_test_login';
        $user->setDefaultValue('isActive');
        $user->setValue('height', null);
        $user->setValue('rating', null);
        $this->objectPersister->commit();

        $primaryKeyField = $usersCollection->getIdentifyField();
        $primaryKeyColumnName = $primaryKeyField->getColumnName();

        $dataSource = $user->getCollection()
            ->getMetadata()
            ->getCollectionDataSource();
        $select = $dataSource->select();
        $select->where()
            ->expr($primaryKeyColumnName, '=', ':objectId');
        $select->bindValue(':objectId', $user->getId(), $primaryKeyField->getDataType());
        $result = $select->execute();
        $values = $result->fetch();

        $this->assertSame(
            'new_test_login',
            $user->getLogin(),
            'Ожидается, что после записи в бд у пользователя обновилось значение для свойства login'
        );
        $this->assertEquals(
            'new_test_login',
            $values['login'],
            'Ожидается, что в бд для пользователя записался login "new_test_login"'
        );
        $this->assertSame(
            true,
            $user->getValue('isActive'),
            'Ожидается, что после записи в бд у пользователя обновилось значение для свойства isActive'
        );
        $this->assertEquals(1, $values['is_active'], 'Ожидается, что в бд для пользователя записался isActive "1"');
        $this->assertSame(
            $oldPassword,
            $user->getPassword(),
            'Ожидается, что свойство пользователя password не изменило своего значения'
        );
        $this->assertNull($values['height'], 'Ожидается, что в бд для пользователя значение поля height равно null');
        $this->assertNull(
            $values['rating'],
            'Ожидается, что в бд для пользователя значение поля rating равно null несмотря на выставленное дефолтное значение'
        );
        $this->assertTrue(
            $oldHeight === 163 && $user->getValue('height') === null,
            'Ожидается, что значение для поля height изначально было 163 и стало null'
        );
        $this->assertTrue(
            $oldRating === 0.0 && $user->getValue('rating') === null,
            'Ожидается, что значение для поля rating по умолчанию было 0 и стало null'
        );

        $this->assertFalse($user->getIsModified(), 'Ожидается, что после commit объект помечается, как неизмененный');

    }

    public function testDelete()
    {
        /**
         * @var User $user
         */
        $usersCollection = $this->collectionManager->getCollection(self::USERS_USER);
        $user = $this->objectManager->registerNewObject(
            $usersCollection,
            $usersCollection->getMetadata()
                ->getBaseType()
        );
        $user->login = 'test_login';
        $this->objectPersister->commit();

        $primaryKeyField = $usersCollection->getIdentifyField();
        $primaryKeyColumnName = $primaryKeyField->getColumnName();

        $dataSource = $user->getCollection()
            ->getMetadata()
            ->getCollectionDataSource();
        $select = $dataSource->select();
        $select->where()
            ->expr($primaryKeyColumnName, '=', ':objectId');
        $select->bindValue(':objectId', $user->getId(), $primaryKeyField->getDataType());

        $this->objectPersister->markAsDeleted($user);
        $this->objectPersister->commit();

        $select->execute();
        $this->assertEquals(0, $select->getTotal(), 'Ожидается, что запись пользователя была удалена из бд');
    }

}

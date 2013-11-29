<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\objectset;

use umi\orm\collection\ICollectionFactory;
use umi\orm\object\IObject;
use umi\orm\objectset\IObjectSet;
use utest\orm\ORMDbTestCase;

/**
 * Тест загрузки объектов
 */
class LoadObjectsTest extends ORMDbTestCase
{

    /**
     * @var IObjectSet $objectsSet
     */
    protected $objectsSet;
    protected $counterId = 1;

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
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {

        $this->objectsSet = $this->getMock('umi\orm\objectset\ObjectSet', ['getQueryResultRow']);
        $this->objectsSet->expects($this->any())
            ->method('getQueryResultRow')
            ->will($this->returnCallback([$this, 'mockGetQueryResultRow']));

        $this->resolveOptionalDependencies($this->objectsSet);
        $this->counterId = 1;
    }

    /**
     * Заглушка для ObjectSet::getQueryResultRow
     * @return bool
     */
    public function mockGetQueryResultRow()
    {

        switch ($this->counterId) {
            case 1:
            {
                return [
                    'users_user:id'       => "1",
                    'users_user:type'     => "users_user.base",
                    'users_user:guid'     => "users_user_1",
                    'users_user:isActive' => "1",
                    'users_user:login'    => "test_login1"
                ];
            }
            case 2:
            {
                return [
                    'id'                  => "1",
                    'users_user:type'     => "users_user.base",
                    'users_user:guid'     => "users_user_1",
                    'users_user:isActive' => "1",
                    'users_user:login'    => "test_login1"
                ];
            }
            case 3:
            {
                return [
                    'users_user:id'       => "1",
                    'users_user:guid'     => "users_user_1",
                    'users_user:isActive' => "1",
                    'users_user:login'    => "test_login1"
                ];
            }
            case 4:
            {
                return [
                    'users_user:id'       => "1",
                    'users_user:type'     => "users_user",
                    'users_user:guid'     => "users_user_1",
                    'users_user:isActive' => "1",
                    'users_user:login'    => "test_login1"
                ];
            }
            case 5:
            {
                return [
                    'users_user:type'     => "users_user.base",
                    'users_user:guid'     => "users_user_1",
                    'users_user:isActive' => "1",
                    'users_user:login'    => "test_login1"
                ];
            }
            case 6:
            {
                return [
                    'users_user:id'   => "1",
                    'users_user:type' => "users_user.base"
                ];
            }
            case 7:
            {
                return [
                    'users_user:id'   => null,
                    'users_user:type' => null
                ];
            }
            default:
            {
                return false;
            }
        }

    }

    public function testLoadObjects()
    {

        $object = $this->objectsSet->fetch();
        $this->assertInstanceOf('umi\orm\object\IObject', $object);
        $this->assertEquals(
            'users_user',
            $object->getCollection()
                ->getName(),
            'Ожидается, что загрузился объект коллекции user'
        );

        $e = null;
        try {
            $object->getValue('password');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке догрузить значения свойств объекта, если объект не существует в базе'
        );
        $this->assertEquals(
            'Cannot load object with id "1" from collection "users_user".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $object->getProperty(IObject::FIELD_IDENTIFY)
            ->setValue(null);
        $e = null;
        try {
            $object->getValue('password');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке догрузить значения свойств объекта, если у него нет id'
        );
        $this->assertEquals('Cannot load object. Object id required.', $e->getMessage(), 'Неверный текст исключения');

        $this->counterId = 2;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить объект с неверными алиасами полей'
        );
        $this->assertEquals(
            'Cannot load objects from data row. Field alias "id" is not correct.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->counterId = 3;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить объект без информации о типе'
        );
        $this->assertEquals(
            'Cannot load object from data row. Information about object type is not found.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->counterId = 4;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить объект с неверной информацией о типе'
        );
        $this->assertEquals(
            'Cannot load object from data row. Object type path "users_user" is not correct.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->counterId = 5;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить объект без информации о первичном ключе'
        );
        $this->assertEquals(
            'Cannot load object. Identify field value is not found.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->counterId = 6;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить объект без информации о GUID'
        );
        $this->assertEquals(
            'Cannot load object. GUID value is not found.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $this->counterId = 7;

        $e = null;
        try {
            $this->objectsSet->fetch();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\LoadEntityException',
            $e,
            'Ожидается искключение при попытке загрузить "пустой" объект'
        );
        $this->assertEquals('Cannot detect main object from data row.', $e->getMessage(), 'Неверный текст исключения');

    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\object;

use umi\orm\collection\ICollectionFactory;
use umi\orm\object\HierarchicObject;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use umi\orm\object\property\ILocalizedProperty;
use umi\orm\toolbox\factory\PropertyFactory;
use utest\orm\mock\collections\User;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для Object
 */
class ObjectTest extends ORMDbTestCase
{

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var IHierarchicObject $blog
     */
    protected $blog;

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
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            false
        ];
    }

    protected function setUpFixtures()
    {
        $propertyFactory = new PropertyFactory();
        $this->resolveOptionalDependencies($propertyFactory);

        $this->blog = new HierarchicObject(
            $this->getCollectionManager()->getCollection(self::BLOGS_BLOG),
            $this->getMetadataManager()->getMetadata(self::BLOGS_BLOG)->getBaseType(),
            $propertyFactory
        );
        $this->resolveOptionalDependencies($this->blog);

        $this->user = new User(
            $this->getCollectionManager()->getCollection(self::USERS_USER),
            $this->getMetadataManager()->getMetadata(self::USERS_USER)->getBaseType(),
            $propertyFactory
        );
        $this->resolveOptionalDependencies($this->user);

        $property = $this->user->getProperty('isActive');
        $property->setInitialValue(
            $property->getField()
                ->getDefaultValue()
        );

        $this->user->getProperty('login')
            ->setInitialValue('test_login');
        $this->user->setInitialValues(
            [
                'password'             => 'test_password',
                'height'               => 182,
                'rating'               => 7.2,
                IObject::FIELD_GUID    => null,
                IObject::FIELD_VERSION => 1
            ]
        );
    }

    public function testInstances()
    {
        $this->assertInstanceOf(
            'umi\orm\object\IHierarchicObject',
            $this->blog,
            'Ожидается, что блог это иерархический объект'
        );
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $this->blog,
            'Ожидается, что блог это еще и обычный объект'
        );
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $this->user,
            'Ожидается, что пользователь это обычный объект'
        );
        $this->assertFalse(
            $this->user instanceof IHierarchicObject,
            'Ожидается, что пользователь это не иерархический объект'
        );
    }

    public function testCollectionsAndHierarchy()
    {
        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleCollection',
            $this->user->getCollection(),
            'Ожидается, что у любого объекта есть коллекция'
        );
        $this->assertEquals(
            self::USERS_USER,
            $this->user->getCollectionName(),
            'Неверное имя коллекции пользователя'
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ICommonHierarchy',
            $this->blog->getCollection()
                ->getCommonHierarchy(),
            'Ожидается, что у иерархического объекта есть иерархия'
        );
    }

    public function testObjectType()
    {
        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $this->user->getType(),
            'Ожидается, что у объекта есть тип'
        );
        $this->assertEquals('base', $this->user->getTypeName(), 'Неверное имя типа объекта');
        $this->assertEquals('users_user.base', $this->user->getTypePath(), 'Неверный путь к типу объекта');
    }

    public function testHasProperty()
    {

        $this->assertFalse($this->user->hasProperty('nonExistentProperty'));
        $this->assertFalse(
            isset($this->user->nonExistentProperty),
            'Ожидается, что у пользователя нет свойства nonExistentProperty'
        );
        $this->assertFalse(
            isset($this->user['nonExistentProperty']),
            'Ожидается, что у пользователя нет свойства nonExistentProperty'
        );

        //не локализуемое свойство
        $this->assertTrue($this->blog->hasProperty('publishTime'));
        $this->assertFalse($this->blog->hasProperty('publishTime', 'localeId'));

        //локализуемое, но не локализованное свойство
        $this->assertTrue($this->user->hasProperty('login'));
        $this->assertFalse($this->user->hasProperty('login', 'localeId'));
        $this->assertTrue(isset($this->user->login), 'Ожидается, что у пользователя есть свойство login');
        $this->assertTrue(isset($this->user['login']), 'Ожидается, что у пользователя есть свойство login');

        //локализуемое и локализованное
        $this->assertTrue($this->blog->hasProperty('title'));
        $this->assertTrue($this->blog->hasProperty('title', 'ru-RU'));
        $this->assertFalse($this->blog->hasProperty('title', 'nonExistentLocaleId'));
    }

    public function testGetProperty()
    {

        $e = null;
        try {
            $this->blog->getProperty('nonExistentProperty');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить свойство, которое не существует'
        );

        $loginProperty = $this->user->getProperty('login');
        $this->assertInstanceOf(
            'umi\orm\object\property\IProperty',
            $loginProperty,
            'Ожидается, что IObject::getProperty() вернет IProperty, если свойство существует'
        );

        /**
         * @var ILocalizedProperty $titleProperty
         */
        $titleProperty = $this->blog->getProperty('title');
        $this->assertEquals(
            'ru-RU',
            $titleProperty->getLocaleId(),
            'Ожидается, что при запросе свойства c локализуемым и локализованным полем '
            . 'будет создано свойство в текущей локали'
        );

        $ruTitle = $this->blog->getProperty('title', 'ru-RU');
        $this->assertTrue(
            $titleProperty === $ruTitle,
            'Ожидается, что свойство, запрошенное с текущей локалью, равно свойству, запрошенному без локали'
        );

        $e = null;
        try {
            $this->blog->getProperty('title', 'nonExistentLocale');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить свойство в локали, которой не существует'
        );
    }

    public function testProperties()
    {
        $user = $this->user;

        $this->assertEquals(
            ['isActive', 'login'],
            array_keys($user->getProperties()),
            'Ожидается, что у объекта всего 2 загруженных свойств'
        );
        $this->assertCount(
            14,
            $user->getAllProperties(),
            'Ожидается, что у базового пользователя всего 14 свойств'
        );

        $user->setValue('login', 'test_login1');
        $this->assertCount(
            1,
            $user->getModifiedProperties(),
            'Ожидается, что у пользователя 1 модифицированное свойство'
        );
        $this->assertTrue(
            $user->getProperty('login')
                ->getIsLoaded(),
            'Ожидается, что свойство логин загружено'
        );
        $this->assertTrue(
            $user->getProperty('login')
                ->getIsValuePrepared(),
            'Ожидается, что свойство логин подготовлено'
        );

        $this->assertEquals(
            [
                'id',
                'guid',
                'type',
                'version',
                'parent',
                'mpath',
                'slug',
                'uri',
                'childCount',
                'order',
                'level',
                'title#ru-RU',
                'title#en-US',
                'title#en-GB',
                'title#ru-UA',
                'publishTime',
                'subscribers',
                'owner'
            ],
            array_keys($this->blog->getAllProperties()),
            'Ожидается, что в списке всех свойств объекта находятся и локализованные свойства'
        );
    }

    public function testPropertiesAccessors()
    {

        $user = $this->user;
        $this->assertSame(
            'test_password',
            $user->getValue('password'),
            'Ожидается, пользователь имеет пароль test_password'
        );
        $this->assertSame('test_password', $user->password, 'Ожидается, пользователь имеет пароль test_password');
        $this->assertSame(
            'test_password',
            $user->getPassword(),
            'Ожидается, пользователь имеет пароль test_password'
        );
        $this->assertSame('test_password', $user['password'], 'Ожидается, пользователь имеет пароль test_password');

        $this->assertSame(182, $user->getValue('height'), 'Ожидается, что пользователь имеет вес 182');
        $this->assertSame(7.2, $user->getValue('rating'), 'Ожидается, что пользователь имеет рейтинг 7.2');

        $this->assertNull(
            $user->getValue('new_login'),
            'Ожидается, что при запросе значения отсутствующего свойства вернется null'
        );
        $this->assertNull($user->getId(), 'Ожидается, что id объекта null, так как это свойство не было загружено');

        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $user->getValue('type'),
            'Ожидается, что при запросе поля type будет возвращен объект IObjectType'
        );
    }

    public function testPropertiesMutators()
    {

        $user = $this->user;
        $user->setValue('login', 'test_login1');
        $this->assertSame(
            'test_login1',
            $user->getValue('login'),
            'Ожидается, после изменения пользователь имеет логин test_login1'
        );

        $user->login = 'test_login2';
        $this->assertSame(
            'test_login2',
            $user->getValue('login'),
            'Ожидается, после изменения пользователь имеет логин test_login2'
        );

        $user->setLogin('test_login3');
        $this->assertSame(
            'test_login3',
            $user->getValue('login'),
            'Ожидается, после изменения пользователь имеет логин test_login3'
        );

        $user->setLogin(null);
        $this->assertNull($user->getValue('login'), 'Ожидается, что значение поля логин имеет пустое значение');

        $user['login'] = 'test_login4';
        $this->assertSame(
            'test_login4',
            $user->getValue('login'),
            'Ожидается, после изменения пользователь имеет логин test_login4'
        );

        unset($user['login']);
        $this->assertNull($user->getValue('login'), 'Ожидается, что значение поля логин имеет пустое значение');
    }

    public function testReadOnlyProperties()
    {
        $user = $this->user;
        $e = null;
        try {
            $user->setValue('type', 'new_type');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\ReadOnlyEntityException',
            $e,
            'Ожидается исключение при попытке уставновить значение для поля, доступного только для чтения'
        );
    }

    public function testDefaultValues()
    {
        $user = $this->user;
        $this->assertSame(true, $user->getValue('isActive'), 'Ожидается дефолтное значение true для поля isActive');

        $user->setValue('isActive', false);
        $this->assertSame(
            false,
            $user->getValue('isActive'),
            'Ожидается, что значение для поля isActive с дефолтного true изменилось на false'
        );

        unset($user->isActive);
        $this->assertSame(
            true,
            $user->getValue('isActive'),
            'Ожидается, что после unset дефолтное значение восстановилось'
        );

        $user->setValue('isActive', false);
        $user->setDefaultValue('isActive');
        $this->assertSame(
            true,
            $user->getValue('isActive'),
            'Ожидается, что после setDefaultValue дефолтное значение восстановилось'
        );
    }

    public function testObjectGUID()
    {
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $this->user->setGUID('9ee6745f-f40d-46d8-8043-d959594628ce'),
            'Ожидается, что IObject::setGUID() вернет себя'
        );
        $this->assertEquals(
            '9ee6745f-f40d-46d8-8043-d959594628ce',
            $this->user->getGUID(),
            'Ожидается, что объекту можно принудительно задать guid'
        );
    }

    public function testObjectVersion()
    {
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $this->user->setVersion(2),
            'Ожидается, что IObject::setVersion() вернет себя'
        );
        $this->assertEquals(
            2,
            $this->user->getVersion(),
            'Ожидается, что объекту можно принудительно задать версию'
        );
    }

    public function testObjectState()
    {
        $user = $this->user;
        $this->assertFalse($user->getIsNew(), 'Ожидается, что по умолчанию объект не новый');

        $user->setLogin('new_test_login');
        $this->assertTrue(
            $user->getIsModified(),
            'Ожидается, что после изменения одного из свойств объект помечается, как измененный'
        );

        $user->rollback();
        $this->assertFalse(
            $user->getIsModified(),
            'Ожидается, что после rollback объект помечается, как неизмененный'
        );
        $this->assertFalse(
            $user->getProperty('login')
                ->getIsModified(),
            'Ожидается, что после rollback все свойства объекта помечаются, как неизмененные'
        );

        $this->assertSame(
            'test_login',
            $user->getLogin(),
            'Ожидается, что после rollback все свойства объекта будут иметь свои начальные значения'
        );

        $user->unload();
        $e = null;
        try {
            $user->id;
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке выполнить операции с выгруженным объектом'
        );
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object;

use umi\orm\collection\ICollectionFactory;
use umi\orm\metadata\IObjectType;
use utest\orm\mock\collections\Supervisor;
use utest\orm\mock\collections\User;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для вычисляемых при сохранении свойств объекта
 */
class ObjectCalculatedPropertiesTest extends ORMDbTestCase
{

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
                ]
            ],
            true
        ];
    }

    public function testObject()
    {

        $userCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        /**
         * @var User $user
         */
        $user = $userCollection->add();
        $user->login = 'simply_user';
        /**
         * @var Supervisor $sv
         */
        $sv = $userCollection->add('supervisor');
        $sv->login = 'supervisor';

        $this->getObjectPersister()->commit();
        $userGuid = $user->getGUID();
        $svGuid = $sv->getGUID();

        $user->unload();
        $sv->unload();

        $loadedUser = $userCollection->get($userGuid);
        $loadedSv = $userCollection->get($svGuid);

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $loadedUser,
            'Ожидается, что метод ISimpleCollection::get() вернет IObject'
        );
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $loadedSv,
            'Ожидается, что метод ISimpleCollection::get() вернет IObject'
        );

        $this->assertEquals(
            'base',
            $loadedUser->getType()
                ->getName(),
            'Ожидается, что имя типа объекта base, т.к. объект создавался без указания имени типа'
        );
        $this->assertEquals('users_user.base', $loadedUser->getTypePath(), 'Неверный путь до типа объекта');
        $this->assertEquals(
            'supervisor',
            $loadedSv->getType()
                ->getName(),
            'Ожидается, что имя типа объекта supervisor, т.к. объект создавался с указанием имени типа supervisor'
        );
        $this->assertEquals('users_user.supervisor', $loadedSv->getTypePath(), 'Неверный путь до типа объекта');

        $this->assertEquals(36, strlen($loadedUser->getGUID()), 'Неверный guid');
        $this->assertEquals(1, $loadedUser->getVersion(), 'Ожидается, что при добавлении у объекта первая версия');

        $this->assertSame(
            true,
            $loadedUser->getValue('isActive'),
            'Ожидается, что у объекта записалось дефолтное значение у поля с типом bool'
        );
        $this->assertSame(
            0.0,
            $loadedUser->getValue('rating'),
            'Ожидается, что у объекта записалось дефолтное значение у поля с типом float'
        );

    }

    public function testHierarchicObject()
    {

        $blogsCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);

        $blog1 = $blogsCollection->add('blog1');
        $blog2 = $blogsCollection->add('blog2', IObjectType::BASE, $blog1);
        $blog3 = $blogsCollection->add('blog3', IObjectType::BASE, $blog1);

        $this->getObjectPersister()->commit();

        $this->assertEquals(1, $blog1->getOrder());
        $this->assertNull($blog1->getParent());
        $this->assertEquals('#1', $blog1->getMaterializedPath());
        $this->assertEquals('//blog1', $blog1->getURI());
        $this->assertEquals('/blog1', $blog1->getURl());
        $this->assertEquals('blog1', $blog1->getSlug());

        $this->assertEquals(2, $blog3->getOrder());
        $this->assertTrue($blog1 === $blog3->getParent());
        $this->assertEquals('#1.3', $blog3->getMaterializedPath());
        $this->assertEquals('//blog1/blog3', $blog3->getURI());
        $this->assertEquals('/blog1/blog3', $blog3->getURL());
        $this->assertEquals('blog3', $blog3->getSlug());

        $this->assertEquals(0, $blog1->getLevel());
        $this->assertEquals(1, $blog2->getLevel());

        $this->assertEquals(1, $blog1->getVersion());
        $this->assertEquals(2, $blog2->getVersion());

        $this->assertEquals(0, $blog2->getChildCount());
        $this->assertEquals(2, $blog1->getChildCount());

    }
}

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
use utest\orm\ORMDbTestCase;

/**
 * Тест работы валидаторов
 *
 */
class ValidatorsTest extends ORMDbTestCase
{
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

    public function testObjectValidators()
    {

        $usersCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $user = $usersCollection->add()
            ->setValue('login', 'first_login')
            ->setValue('height', 153)
            ->setValue('email', 'test@umisoft.ru');

        $user->validate();

        $this->assertTrue($user->validate(), 'Ожидается, что объект должен пройти валидацию');
        $this->assertCount(0, $user->getValidationErrors(), 'Ожидается, что нет ошибок валидации');

        $user->setValue('login', '12');

        $this->assertFalse(
            $user->validate(),
            'Ожидается, что пользователь с логином меньше 3 знаков не пройдет валидацию'
        );
        $this->assertEquals(
            ['login' => ['Login is shorter than 3 symbols']],
            $user->getValidationErrors(),
            'Ожидается ошибка о длине логина'
        );

        $user
            ->setValue('login', 'first_login')
            ->setValue('height', 1);

        $this->assertFalse(
            $user->validate(),
            'Ожидается, что пользователь с ростом меньше 2х чисел не пройдет валидацию'
        );
        $this->assertEquals(
            ['height' => ['String does not meet regular expression.']],
            $user->getValidationErrors(),
            'Ожидается одна ошибке о неверном росте, так предыдущие ошибки должны были очиститься'
        );

    }

    public function testWrongValidator()
    {

        $usersCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $user = $usersCollection->add()
            ->setValue('login', 'first_login')
            ->setValue('height', 153)
            ->setValue('email', 'test@umisoft.ru')
            ->setValue('rating', 7);

        $this->assertTrue(
            method_exists($user, 'validateRating'),
            'Ожидается, что метод для валидации рейтинга существует'
        );
        $this->assertTrue(
            $user->validate(),
            'Ожидается, что объект должен пройти валидацию, если валидатор ничего не отдает'
        );
    }

    public function testManagerValidators()
    {

        $usersCollection = $this->getCollectionManager()->getCollection(self::USERS_USER);
        $user = $usersCollection->add()
            ->setValue('login', 'first_login')
            ->setValue('height', 153)
            ->setValue('email', 'test@umisoft.ru');

        $this->getObjectPersister()->commit();
        $this->assertTrue($user->validate(), 'Ожидается, что объект должен пройти валидацию, если он не модифицирован');

        $user->setValue('login', '12');

        $usersCollection->add()
            ->setValue('login', 'second_login')
            ->setValue('height', 1);

        $invalidObjects = $this->getObjectPersister()->getInvalidObjects();
        $this->assertCount(2, $invalidObjects, 'Ожидаются два невалидных объекта');

    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection;

use utest\orm\ORMDbTestCase;

class CollectionTest extends ORMDbTestCase
{

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

    public function testGetExceptions()
    {
        $usersCollection = $this->collectionManager->getCollection(self::USERS_USER);

        $e = null;
        try {
            $usersCollection->getById(1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить несуществующий объект'
        );
        $this->assertEquals(
            'Cannot get object with id "1" from collection "users_user".',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $usersCollection->get(1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке получить объект по GUID неверного формата'
        );
        $this->assertEquals(
            'Cannot get object by GUID "1". Wrong GUID format.',
            $e->getMessage(),
            'Неверный текст исключения'
        );

        $e = null;
        try {
            $usersCollection->get('00000000-0000-0000-0000-000000000000');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить объект по несуществующему GUID'
        );
        $this->assertEquals(
            'Cannot get object with GUID "00000000-0000-0000-0000-000000000000" from collection "users_user".',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

}

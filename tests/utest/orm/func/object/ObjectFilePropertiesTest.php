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
use umi\orm\object\property\file\File;
use utest\orm\ORMDbTestCase;

/**
 * Тесты свойства типа файл
 */
class ObjectFilePropertiesTest extends ORMDbTestCase
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
                self::USERS_PROFILE => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::GUIDES_COUNTRY            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::GUIDES_CITY            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    public function testFileProperty()
    {
        $profileCollection = $this->getCollectionManager()->getCollection(self::USERS_PROFILE);
        $profile = $profileCollection->add('natural_person');
        $profileGuid = $profile->getGUID();

        /**
         * @var File $file
         */
        $file = $profile->getValue('image');
        $this->assertInstanceOf('umi\orm\object\property\file\File', $file);

        $e = null;
        try {
            $profile->setValue('image', 'test.jpg');
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается исключение при попытке выставить значение для свойства типа файл напрямую'
        );

        $this->assertInstanceOf('umi\orm\object\property\file\File', $file->setFilePath(TESTS_ROOT . '/utest/orm/mock/files/test.txt'));
        $this->assertEquals('test.txt', $file->getFileInfo()->getFilename());

        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $profile = $profileCollection->get($profileGuid);
        /**
         * @var File $file
         */
        $file = $profile->getValue('image');
        $this->assertInstanceOf('umi\orm\object\property\file\File', $file);
        $this->assertEquals('test.txt', $file->getFileInfo()->getFilename());

        $file->clear();

        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $profile = $profileCollection->get($profileGuid);
        /**
         * @var File $file
         */
        $file = $profile->getValue('image');
        $this->assertInstanceOf('umi\orm\object\property\file\File', $file);
        $e = null;
        try {
            $file->getFileInfo();
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что путь к файлу в базе был сброшен и теперь нельзя получить информацию о файле'
        );

    }
}
 
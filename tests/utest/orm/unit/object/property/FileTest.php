<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\object\property;

use umi\orm\metadata\field\special\FileField;
use umi\orm\object\IObject;
use umi\orm\object\property\file\File;
use umi\orm\object\property\file\FileProperty;
use umi\orm\object\property\file\IFileProperty;
use utest\orm\ORMTestCase;

/**
 * Тесты значения свойства типа файл
 */
class FileTest extends ORMTestCase
{
    /**
     * @var IFileProperty $fileProperty
     */
    private $fileProperty;

    protected function setUpFixtures()
    {
        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', [], [], '', false);
        $fileField = new FileField(
            'file',
            [
                'sourcePath' => '',
                'sourceURI' => ''
            ]
        );
        $this->fileProperty = new FileProperty($object, $fileField);
    }

    public function testEmptyFile()
    {
        $file = new File($this->fileProperty, TESTS_ROOT . '/utest/orm/mock', 'http://example.com');

        $e = null;
        try {
            $file->getFileInfo();
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить информацию о файле без пути'
        );

        $e = null;
        try {
            $file->getURI();
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить URI файла без пути'
        );
    }

    public function testFileWithValue()
    {
        $file = new File($this->fileProperty, TESTS_ROOT . '/utest/orm/mock', 'http://example.com', 'files/test.txt');

        $this->assertEquals('test.txt', $file->getFileInfo()->getFilename());
        $this->assertEquals('http://example.com/files/test.txt', $file->getURI());
    }

    public function testSetFilePath()
    {
        $file = new File($this->fileProperty, TESTS_ROOT . '/utest/orm/mock', 'http://example.com');

        $e = null;
        try {
            $file->setFilePath('nonExistentFile.jpg');
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что нельзя выставить путь к несуществующему файлу'
        );

        $e = null;
        try {
            $file->setFilePath(__FILE__);
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что нельзя выставить путь к файлу, находящемуся вне указанной для поля директории'
        );

        $file->setFilePath(TESTS_ROOT . '/utest/orm/mock/files/test.txt');
        $this->assertEquals('test.txt', $file->getFileInfo()->getFilename());
        $this->assertEquals('http://example.com/files/test.txt', $file->getURI());
    }

    public function testClear()
    {
        $file = new File($this->fileProperty, TESTS_ROOT . '/utest/orm/mock', 'http://example.com', 'files/test.txt');
        $file->clear();

        $e = null;
        try {
            $file->getFileInfo();
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить информацию о файле после сброса пути'
        );

        $e = null;
        try {
            $file->getURI();
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя получить URI файла после сброса пути'
        );
    }
}
 
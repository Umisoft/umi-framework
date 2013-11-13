<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit;

use umi\config\entity\value\ConfigValue;
use umi\config\entity\value\IConfigValue;
use umi\config\exception\RuntimeException;
use umi\config\exception\UnexpectedValueException;
use umi\config\io\IConfigIO;
use umi\config\io\reader\IReader;
use umi\config\io\reader\PhpFileReader;
use utest\TestCase;

/**
 * Тесты reader'а php конфигурации.
 */
class PhpFileReaderTest extends TestCase
{
    /**
     * @var IReader $reader
     */
    private $reader;

    public function setUpFixtures()
    {
        /**
         * @var IConfigIO $configIO
         */
        $configIO = $this->getTestToolkit()
            ->getService('umi\config\io\IConfigIO');
        $configIO->registerAlias(
            '~/test',
            __DIR__ . '/data/php/master',
            __DIR__ . '/data/php/local'
        )
            ->registerAlias(
                '~/master-test',
                __DIR__ . '/data/php/master'
            );

        $this->reader = new PhpFileReader();
        $this->resolveOptionalDependencies($this->reader);
    }

    public function testBasic()
    {
        $config = $this->reader->read('~/test/main.php');

        $this->assertEquals('~/test/main.php', $config->getAlias());
        $source = $config->getSource();

        $masterValue = new ConfigValue();
        $masterValue->set('masterValue', IConfigValue::KEY_MASTER)
            ->save();

        $this->resolveOptionalDependencies($masterValue);

        $this->assertEquals(
            $masterValue,
            $source['master'],
            'Ожидается, что объект значения будет хранить только мастер значение.'
        );

        $localValue = new ConfigValue();
        $localValue->set('localValue', IConfigValue::KEY_LOCAL)
            ->save();
        $this->resolveOptionalDependencies($localValue);

        $this->assertEquals(
            $localValue,
            $source['local'],
            'Ожидается, что объект значения будет хранить только локальное значение.'
        );

        $value = new ConfigValue();
        $value->set('masterValue', IConfigValue::KEY_MASTER)
            ->set('localValue', IConfigValue::KEY_LOCAL)
            ->save();
        $this->resolveOptionalDependencies($value);

        $this->assertEquals(
            $value,
            $source['value'],
            'Ожидается, что объект значения будет хранить локальное и мастер значение.'
        );
    }

    public function testInnerArrayMerge()
    {
        $config = $this->reader->read('~/test/newLocalValues.php')
            ->getSource();

        $this->assertTrue(isset($config['key']['myLocalInner']));

        $localValue = new ConfigValue();
        $localValue->set('local', IConfigValue::KEY_LOCAL)
            ->save();
        $this->resolveOptionalDependencies($localValue);

        $this->assertEquals($localValue, $config['key']['myLocalInner']['inner']);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function noMasterConfiguration()
    {
        $this->reader->read('~/test/invalid-filename.php');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function invalidMergeArrayWithScalar()
    {
        $this->reader->read('~/test/invalidMerge.php');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function invalidMergeScalarWithArray()
    {
        $this->reader->read('~/test/invalidMerge2.php');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function invalidConfigValue()
    {
        $this->reader->read('~/test/invalidConfiguration.php');
    }

    public function testPartial()
    {
        $config = $this->reader->read('~/test/partialMain.php');

        $source = $config->getSource();
        $this->assertInstanceOf('umi\config\entity\IConfigSource', $source['partial']);

        $this->assertEquals('Local partial value', $config->get('partial/part-inner'));
    }

    public function testLazy()
    {
        $config = $this->reader->read('~/test/lazyMain.php');

        $source = $config->getSource();
        $this->assertInstanceOf('umi\config\entity\IConfigSource', $source['partial']);

        $this->assertEquals('Local partial value', $config->get('partial/part-inner'));
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongPartialFile()
    {
        $this->reader->read('~/test/partialWrong.php');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function entityOverwriting()
    {
        $this->reader->read('~/test/partialOverwriting.php');
    }

    public function testOnlyMasterConfig()
    {
        $config = $this->reader->read('~/master-test/main.php');
        $this->assertEquals('masterValue', $config->get('value'));
    }
}
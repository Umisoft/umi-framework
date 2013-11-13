<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit;

use umi\config\entity\IConfigSource;
use umi\config\exception\RuntimeException;
use umi\config\io\IConfigIO;
use umi\config\io\reader\PhpFileReader;
use umi\config\io\writer\PhpFileWriter;
use utest\TestCase;

/**
 * Тесты reader'а php конфигурации.
 */
class PhpFileWriterTest extends TestCase
{
    /**
     * @var PhpFileWriter $writer
     */
    private $writer;
    /**
     * @var PhpFileReader $reader
     */
    private $reader;
    /**
     * @var string $fsLocalDir локальная директория
     */
    protected $fsLocalDir = '/data/php/local';

    public function setUpFixtures()
    {
        $this->fsLocalDir = __DIR__ . $this->fsLocalDir;
        @mkdir($this->fsLocalDir);

        /**
         * @var IConfigIO $configIO
         */
        $configIO = $this->getTestToolkit()
            ->getService('umi\config\io\IConfigIO');

        $configIO->registerAlias(
            '~/test',
            __DIR__ . '/data/php/master',
            $this->fsLocalDir
        )
            ->registerAlias('~/master', __DIR__ . '/data/php/master');

        $this->writer = new PhpFileWriter();
        $this->resolveOptionalDependencies($this->writer);

        $this->reader = new PhpFileReader();
        $this->resolveOptionalDependencies($this->reader);
    }

    public function testEmptyLocal()
    {
        $cfg = $this->getConfig('~/test/basic.php');
        $this->writer->write($cfg);

        /** @noinspection PhpIncludeInspection */
        $this->assertEmpty([], require $this->fsLocalDir . '/basic.php');
    }

    public function testChangedLocal()
    {
        $cfg = $this->getConfig('~/test/basic.php');

        $cfg['key1'] = 'local';

        $this->writer->write($cfg);

        /** @noinspection PhpIncludeInspection */
        $this->assertEquals(
            [
                'key1' => 'local'
            ],
            require $this->fsLocalDir . '/basic.php'
        );
    }

    public function testRemoveEmptyElements()
    {
        $cfg = $this->getConfig('~/test/basic.php');

        $cfg['key/key1'] = 'first';
        $cfg['key1'] = 'local';
        $this->writer->write($cfg);

        /** @noinspection PhpIncludeInspection */
        $this->assertEquals(
            [
                'key1' => 'local',
                'key'  => [
                    'key1' => 'first',
                ]
            ],
            require $this->fsLocalDir . '/basic.php'
        );

        $cfg->del('key/key1');
        $this->writer->write($cfg);

        /** @noinspection PhpIncludeInspection */
        $this->assertEquals(
            [
                'key1' => 'local',
            ],
            require $this->fsLocalDir . '/basic.php'
        );
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function onlyMasterConfig()
    {
        $basic = $this->getConfig('~/master/basic.php');

        $this->writer->write($basic);
    }

    public function testPartial()
    {
        $cfg = $this->getConfig('~/test/partMain.php');

        $cfg['key'] = 'localMain';
        $cfg['part/key'] = 'localPart';
        $this->writer->write($cfg);

        /** @noinspection PhpIncludeInspection */
        $this->assertEquals(
            [
                'key' => 'localMain',
            ],
            require $this->fsLocalDir . '/partMain.php'
        );

        /** @noinspection PhpIncludeInspection */
        $this->assertEquals(
            [
                'key' => 'localPart',
            ],
            require $this->fsLocalDir . '/part.php'
        );
    }

    /**
     * Возвращает конфигурацию по её символическому имени.
     * @param string $alias символическое имя
     * @return IConfigSource
     */
    protected function getConfig($alias)
    {
        return $this->reader->read($alias);
    }
}
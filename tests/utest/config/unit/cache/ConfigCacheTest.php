<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config;

use umi\config\cache\ConfigCacheEngine;
use umi\config\entity\ConfigSource;
use umi\config\entity\LazyConfigSource;
use umi\config\entity\value\ConfigValue;
use umi\config\entity\value\IConfigValue;
use umi\config\exception\RuntimeException;
use utest\config\ConfigTestCase;

class ConfigCacheTest extends ConfigTestCase
{
    /**
     * @var ConfigCacheEngine $cacheEngine
     */
    private $cacheEngine;
    /**
     * @var string $directory директория с кешем
     */
    private $directory;

    public function setUpFixtures()
    {
        $this->directory = __DIR__ . '/data';
        $this->cacheEngine = new ConfigCacheEngine(['directory' => $this->directory]);

        @mkdir($this->directory);
    }

    public function tearDownFixtures()
    {
        $files = scandir($this->directory);

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink($this->directory . '/' . $file);
            }
        }

        @rmdir($this->directory);
    }

    public function testCacheEngineLoadSave()
    {
        $source = [
            'key' => new ConfigValue([
                    IConfigValue::KEY_LOCAL  => 'localValue',
                    IConfigValue::KEY_MASTER => 'masterValue'
                ])
        ];
        $config = new ConfigSource($source, '~/test.php');

        $this->cacheEngine->save($config);

        $this->assertTrue($this->cacheEngine->isActual('~/test.php', time() - 3600));

        $saved = $this->cacheEngine->load('~/test.php');
        $this->assertEquals($config, $saved);

        $this->markTestIncomplete('Waiting toolbox refactoring.');
    }

    public function testCacheSeparateFiles()
    {
        $source = [
            'key' => new LazyConfigSource('~/part.php'),
        ];

        $config = new ConfigSource($source, '~/test.php');

        $this->markTestIncomplete('Waiting toolbox refactoring.');
    }

    public function testIsActual()
    {
        $this->assertFalse($this->cacheEngine->isActual('~/test2.php', time()));
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function loadInvalidAlias()
    {
        $this->cacheEngine->load('~/wrong.php');
    }

}
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
use umi\config\exception\RuntimeException;
use umi\config\io\IConfigIO;
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
    /**
     * @var IConfigIO $ioService
     */
    private $ioService;

    public function setUpFixtures()
    {
        /** @var IConfigIO $ioService */
        $this->ioService = $this->getTestToolkit()
            ->getService('umi\config\io\IConfigIO');

        $this->ioService->registerAlias('~', __DIR__ . '/fixtures/master',  __DIR__ . '/fixtures/local');

        $this->directory = __DIR__ . '/data';
        $this->cacheEngine = new ConfigCacheEngine(['directory' => $this->directory]);
        $this->resolveOptionalDependencies($this->cacheEngine);

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
        $config = $this->ioService->read('~/test.php');
        $this->cacheEngine->save($config);

        $this->assertTrue($this->cacheEngine->isActual('~/test.php', time() - 3600));

        $saved = $this->cacheEngine->load('~/test.php');
        $this->assertEquals($config, $saved);
    }

    public function testCacheSeparateFiles()
    {
        $config = $this->ioService->read('~/test2.php');
        $this->assertEquals('value', $config->get('part/key'));

        $this->cacheEngine->save($config);

        $this->assertTrue($this->cacheEngine->isActual('~/test2.php', time() - 3600));

        $saved = $this->cacheEngine->load('~/test2.php');
        $this->assertEquals('value', $saved->get('part/key'));

        $this->assertEquals($config, $saved);
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

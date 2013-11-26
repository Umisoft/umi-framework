<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit\toolbox;

use umi\config\exception\RuntimeException;
use umi\config\io\ConfigIO;
use umi\config\io\IConfigIO;
use umi\config\io\reader\PhpFileReader;
use umi\config\io\writer\PhpFileWriter;
use utest\config\ConfigTestCase;

/**
 * Тесты I/O сервиса конфигурации.
 */
class ConfigIOTest extends ConfigTestCase
{
    /**
     * @var IConfigIO $tools
     */
    private $configIO;

    public function setUpFixtures()
    {
        $reader = new PhpFileReader();
        $writer = new PhpFileWriter();
        $this->resolveOptionalDependencies($reader);
        $this->resolveOptionalDependencies($writer);

        $this->configIO = new ConfigIO($reader, $writer);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function alreadyRegisteredAlias()
    {
        $this->configIO->registerAlias('~/alias', 'dir', 'local');
        $this->configIO->registerAlias('~/alias', 'dir', 'local');
    }

    public function testGetAlias()
    {
        $this->configIO->registerAlias('~/alias/inner', '2', '2');
        $this->configIO->registerAlias('~/alias', '1', '1');
        $this->configIO->registerAlias('~/dir/alias', '3', '3');

        $this->assertEquals(['1/1.php', '1/1.php'], $this->configIO->getFilesByAlias('~/alias/1.php'));
        $this->assertEquals(['2/2.php', '2/2.php'], $this->configIO->getFilesByAlias('~/alias/inner/2.php'));
        $this->assertEquals(['3/3.php', '3/3.php'], $this->configIO->getFilesByAlias('~/dir/alias/3.php'));
    }

    public function testOnlyMasterDirectory()
    {
        $this->configIO->registerAlias('~/test', 'master');
        $files = $this->configIO->getFilesByAlias('~/test');
        $this->assertEquals(['master'], $files);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function notFoundPath()
    {
        $this->configIO->getFilesByAlias('~/not/found/script.php');
    }

}
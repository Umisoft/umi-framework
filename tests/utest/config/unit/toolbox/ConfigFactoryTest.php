<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit\toolbox;

use umi\config\entity\factory\IConfigEntityFactory;
use umi\config\exception\OutOfBoundsException;
use umi\config\toolbox\factory\ConfigEntityFactory;
use umi\toolkit\exception\DomainException;
use utest\config\ConfigTestCase;

/**
 * Тесты фабрики сущностей конфигурации.
 */
class ConfigFactoryTest extends ConfigTestCase
{
    /***
     * @var ConfigEntityFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ConfigEntityFactory();

        $this->resolveOptionalDependencies($this->factory);
    }

    public function testValue()
    {
        $value = $this->factory->createValue();
        $this->assertInstanceOf('umi\config\entity\value\IConfigValue', $value);
        $this->assertNotSame($value, $this->factory->createValue());
    }

    /**
     * @test
     * @expectedException DomainException
     */
    public function wrongValueClass()
    {
        $this->factory->valueClass = '\StdClass';
        $this->factory->createValue();
    }

    public function testSource()
    {
        $s = [];
        $src = $this->factory->createConfigSource('~/alias.php', $s);
        $this->assertInstanceOf('umi\config\entity\IConfigSource', $src);
        $this->assertNotSame($src, $this->factory->createConfigSource('~/alias.php', $s));
    }

    /**
     * @test
     * @expectedException DomainException
     */
    public function wrongSource()
    {
        $s = [];
        $this->factory->configSourceClass = '\StdClass';
        $this->factory->createConfigSource('~/alias.php', $s);
    }

    public function testSeparateSource()
    {
        $src = $this->factory->createSeparateConfigSource(IConfigEntityFactory::SEPARATE_LAZY, '~/alias.php');
        $this->assertInstanceOf('umi\config\entity\ISeparateConfigSource', $src);
        $this->assertNotSame(
            $src,
            $this->factory->createSeparateConfigSource(IConfigEntityFactory::SEPARATE_LAZY, '~/alias.php')
        );
    }

    /**
     * @test
     * @expectedException DomainException
     */
    public function wrongSeparateSource()
    {
        $this->factory->separateConfigClasses['lazy'] = '\StdClass';
        $this->factory->createSeparateConfigSource(IConfigEntityFactory::SEPARATE_LAZY, '~/alias.php');
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function invalidSeparateSource()
    {
        $this->factory->createSeparateConfigSource('wrong', '~/alias.php');
    }
}
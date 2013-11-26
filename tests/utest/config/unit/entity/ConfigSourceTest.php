<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit;

use umi\config\entity\ConfigSource;
use umi\config\entity\IConfigSource;
use umi\config\entity\value\ConfigValue;
use umi\config\entity\value\IConfigValue;
use utest\config\ConfigTestCase;

/**
 * Тесты конфигурации.
 */
class ConfigSourceTest extends ConfigTestCase
{
    /**
     * @var IConfigSource $source
     */
    private $cfgSource;
    /**
     * @var array $src
     */
    private $src;

    public function setUpFixtures()
    {
        $key1 = new ConfigValue();
        $key1->set('master', IConfigValue::KEY_MASTER);
        $key1->set('local', IConfigValue::KEY_LOCAL);

        $key2 = new ConfigValue();
        $key2->set('local', IConfigValue::KEY_LOCAL);

        $this->src = [
            'key1' => $key1,
            'key2' => $key2,
        ];

        $this->cfgSource = new ConfigSource($this->src, '~/alias.php');
        $this->resolveOptionalDependencies($this->cfgSource);
    }

    public function testAlias()
    {
        $this->assertEquals('~/alias.php', $this->cfgSource->getAlias());
    }

    public function testSource()
    {
        $this->assertSame($this->src, $this->cfgSource->getSource());
    }
}
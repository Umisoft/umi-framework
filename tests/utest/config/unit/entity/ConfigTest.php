<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit;

use umi\config\entity\Config;
use umi\config\entity\IConfig;
use umi\config\entity\value\ConfigValue;
use umi\config\entity\value\IConfigValue;
use umi\config\exception\InvalidArgumentException;
use umi\config\exception\UnexpectedValueException;
use utest\TestCase;

/**
 * Тесты конфигурации.
 */
class ConfigTest extends TestCase
{
    /**
     * @var IConfig $source
     */
    private $config;

    public function setUpFixtures()
    {
        $key1 = new ConfigValue([
            IConfigValue::KEY_MASTER => 'master',
            IConfigValue::KEY_LOCAL  => 'local'
        ]);

        $key2 = new ConfigValue([
            IConfigValue::KEY_LOCAL => 'local'
        ]);

        $key4src = [
            'innerKey1' => $key1
        ];

        $source = [
            'key1' => $key1,
            'key2' => $key2,
            'key3' => [
                'inner' => $key1
            ],
            'key4' => new Config($key4src)
        ];

        $this->resolveOptionalDependencies($source['key4']);

        $this->config = new Config($source);
        $this->resolveOptionalDependencies($this->config);
    }

    public function testEmptyKey()
    {
        $this->assertFalse($this->config->has('key3/empty'));
        $this->assertNull($this->config->get('key3/empty'));

        $this->assertSame($this->config, $this->config->del('key3/empty'));
        $this->assertSame($this->config, $this->config->reset('key3/empty'));
    }

    public function testBasic()
    {
        $this->assertEquals(
            'local',
            $this->config->get('key1'),
            'Ожидается, что будет получено локальное значение.'
        );

        $this->assertTrue($this->config->has('key1'));

        $this->config->set('key1', 'session');
        $this->assertEquals(
            'session',
            $this->config->get('key1'),
            'Ожидается, что будет получено сессионное значение.'
        );

        $this->config->reset('key1');
        $this->assertEquals(
            'local',
            $this->config->get('key1'),
            'Ожидается, что будет получено локальное значение.'
        );

        $this->config->del('key1');
        $this->assertEquals(
            'master',
            $this->config->get('key1'),
            'Ожидается, что будет получено локальное значение.'
        );
    }

    public function testInnerBasic()
    {
        $this->assertEquals(
            'local',
            $this->config->get('key3/inner'),
            'Ожидается, что будет получено локальное значение.'
        );

        $this->assertTrue($this->config->has('key3/inner'));

        $this->config->set('key3/inner', 'session');
        $this->assertEquals(
            'session',
            $this->config->get('key3/inner'),
            'Ожидается, что будет получено сессионное значение.'
        );

        $this->config->reset('key3/inner');
        $this->assertEquals(
            'local',
            $this->config->get('key3/inner'),
            'Ожидается, что будет получено локальное значение.'
        );

        $this->config->del('key3/inner');
        $this->assertEquals(
            'master',
            $this->config->get('key3/inner'),
            'Ожидается, что будет получено локальное значение.'
        );
    }

    /**
     * @test установки скалярного значения в массив
     * @expectedException InvalidArgumentException
     */
    public function setInvalidValue()
    {
        $this->config->set('key3', 'scalar');
    }

    /**
     * @test установки скалярного значения в конфиг.
     * @expectedException InvalidArgumentException
     */
    public function setInvalidValue2()
    {
        $this->config->set('key4', 'scalar');
    }

    /**
     * @test установки значения в дочерний ключ к скалярному элементу
     * @expectedException InvalidArgumentException
     */
    public function setInvalidKey()
    {
        $this->config->set('key1/inner', 'scalar');
    }

    /**
     * @test получения значения из дочернего ключа к скалярному элементу
     * @expectedException InvalidArgumentException
     */
    public function getInvalidKey()
    {
        $this->config->get('key1/inner');
    }

    /**
     * @test удаления значения из дочернего ключа к скалярному элементу
     * @expectedException InvalidArgumentException
     */
    public function delInvalidKey()
    {
        $this->config->del('key1/inner');
    }

    /**
     * @test сброса значения из дочернего ключа к скалярному элементу
     * @expectedException InvalidArgumentException
     */
    public function resetInvalidKey()
    {
        $this->config->reset('key1/inner');
    }

    public function testIterator()
    {
        /**
         * @var IConfig[]|mixed $array
         */
        $array = iterator_to_array($this->config);

        $this->assertEquals('local', $array['key1']);
        $this->assertEquals('local', $array['key2']);

        $this->assertEquals(['inner' => 'local'], $array['key3']->toArray());
        $this->assertEquals(['innerKey1' => 'local'], $array['key4']->toArray());

        $this->assertEquals(4, count($this->config));
    }

    public function testNewValue()
    {
        $this->config->set('key5', 'Test string');
        $this->assertEquals('Test string', $this->config->get('key5'));

        $this->config->set('key6/inner', 'Test string');
        $this->assertEquals(
            'Test string',
            $this->config->get('key6')
                ->get('inner')
        );
    }

    public function testMergeValues()
    {
        $this->config->merge(
            [
                'key1' => 'merged',
                'key2' => 'merged',
                'key3' => [
                    'inner' => 'merged'
                ],
                'key4' => [
                    'innerKey1' => 'merged'
                ],
                'key5' => 'merged'
            ]
        );

        $this->assertEquals(
            [
                'key1' => 'merged',
                'key2' => 'merged',
                'key3' => [
                    'inner' => 'merged'
                ],
                'key4' => [
                    'innerKey1' => 'merged'
                ],
                'key5' => 'merged'
            ],
            $this->config->toArray()
        );
    }

    public function testSetArrayToConfig()
    {
        $this->config->set(
            'key4',
            [
                'innerKey2' => 'done'
            ]
        );

        $this->assertEquals('done', $this->config->get('key4/innerKey2'));
    }

    public function testSetArrayToArray()
    {
        $this->config->set(
            'key3',
            [
                'inner2' => 'done'
            ]
        );

        $this->assertEquals('done', $this->config->get('key3/inner2'));
    }

    public function testSetValueToChildConfig()
    {
        $this->config->set('key4/into/inner', 'test');
        $this->assertEquals('test', $this->config->get('key4/into/inner'));
    }

    public function testGetValues()
    {
        $this->assertInstanceOf('umi\config\entity\IConfig', $this->config->get('key4'));
        $this->assertInstanceOf('umi\config\entity\IConfig', $this->config->get('key3'));
    }

    public function testHasValue()
    {
        $this->assertTrue($this->config->has('key1'));
        $this->assertFalse($this->config->has('key1/inner'));

        $this->assertTrue($this->config->has('key3'));

        $this->config->set('key4/into/inner', 'test');
        $this->assertTrue($this->config->has('key4/into/inner'));
    }

    public function testResetValue()
    {
        $this->config->set('key1', 'newValue');
        $this->config->set('key2', 'newValue');
        $this->config->reset('key1');
        $this->assertEquals('local', $this->config->get('key1'));
        $this->assertEquals('newValue', $this->config->get('key2'));

        $this->config->set('key3/inner', 'newValue');
        $this->config->reset('key3');
        $this->assertEquals('local', $this->config->get('key3/inner'));

        $this->config->set('key4/inner', 'newValue');
        $this->config->reset('key4');
        $this->assertEquals('local', $this->config->get('key3/inner'));

        $this->config->set('key1', 'newValue');
        $this->config->set('key3/inner', 'newValue');
        $this->config->set('key4/innerKey1', 'newValue');
        $this->config->reset();

        $this->assertEquals('local', $this->config->get('key1'));
        $this->assertEquals('local', $this->config->get('key3/inner'));
        $this->assertEquals('local', $this->config->get('key4/innerKey1'));

        $this->config->set('key4/innerKey1', 'newValue');
        $this->config->reset('key4/innerKey1');
        $this->assertEquals('local', $this->config->get('key4/innerKey1'));
    }

    public function testDeleteValue()
    {
        $this->config->set('key1', 'newValue');
        $this->config->set('key2', 'newValue');
        $this->config->del('key1');
        $this->assertEquals('master', $this->config->get('key1'));
        $this->assertEquals('newValue', $this->config->get('key2'));

        $this->config->set('key4/innerKey1', 'newValue');
        $this->config->del('key4/innerKey1');
        $this->assertEquals('master', $this->config->get('key4/innerKey1'));
    }

    public function testMerge()
    {
        $this->config->merge(
            [
                'key5' => [
                    'inner' => [
                        'into' => 'value'
                    ]
                ]
            ]
        );

        $this->assertEquals('value', $this->config->get('key5/inner/into'));
    }

    public function testClone()
    {
        $config2 = clone $this->config;

        $config2->set('key1', 'copied');
        $this->assertNotEquals('copied', $this->config->get('key1'));
    }

    /**
     * @test удаления дочернего конфига
     * @expectedException UnexpectedValueException
     */
    public function deleteSubConfig()
    {
        $this->config->del('key4');
    }

    /**
     * @test удаления массива
     * @expectedException UnexpectedValueException
     */
    public function deleteArrayValue()
    {
        $this->config->del('key3');
    }

    /**
     * @test распаковки битого конфига
     * @expectedException UnexpectedValueException
     */
    public function corruptedToArray()
    {
        $src = [
            'key' => 'value'
        ];
        $config = new Config($src);
        $config->toArray();
    }

    /**
     * @test получения значения из битого конфига
     * @expectedException UnexpectedValueException
     */
    public function getCorruptedValue()
    {
        $src = [
            'key' => 'value'
        ];
        $config = new Config($src);
        $config->get('key');
    }

    /**
     * @test сброса значения из битого конфига
     * @expectedException UnexpectedValueException
     */
    public function resetCorruptedValue()
    {
        $src = [
            'key' => 'value'
        ];
        $config = new Config($src);
        $config->reset('key');
    }

    /**
     * @test удаления значения из битого конфига
     * @expectedException UnexpectedValueException
     */
    public function deleteCorruptedValue()
    {
        $src = [
            'key' => 'value'
        ];
        $config = new Config($src);
        $config->del('key');
    }

    /**
     * @test удаления значения из битого конфига
     * @expectedException UnexpectedValueException
     */
    public function mergeCorruptedValue()
    {
        $src = [
            'key' => 'value'
        ];
        $config = new Config($src);
        $config->merge(
            [
                'key' => 'value2'
            ]
        );
    }

    /**
     * @test слияния с неверной структурой конфига
     * @expectedException InvalidArgumentException
     */
    public function invalidMerge()
    {
        $this->config->merge(
            [
                'key3' => 'scalar'
            ]
        );
    }

    /**
     * @test слияния с неверной структурой конфига
     * @expectedException InvalidArgumentException
     */
    public function invalidMerge2()
    {
        $this->config->merge(
            [
                'key4' => 'scalar'
            ]
        );
    }
}
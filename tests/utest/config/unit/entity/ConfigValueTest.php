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
use umi\config\exception\InvalidArgumentException;
use utest\config\ConfigTestCase;

/**
 * Тесты значения конфигурации.
 */
class ConfigValueTest extends ConfigTestCase
{
    /**
     * @var IConfigValue $value
     */
    private $value;

    public function setUpFixtures()
    {
        $this->value = new ConfigValue([
            IConfigValue::KEY_MASTER => 1,
            IConfigValue::KEY_LOCAL  => 2,
        ]);
    }

    public function testBasic()
    {
        $this->value = new ConfigValue();

        $this->assertFalse($this->value->has(), 'Ожидается, что ни одного значения не существует.');
        $this->assertNull(
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что локальное значение не существует.'
        );

        $this->value->set(42);
        $this->assertEquals(42, $this->value->get(), 'Ожидается, что сессионное значение будет получено.');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function setInvalidValue()
    {
        $this->value->set(['test']);
    }

    public function testGetSet()
    {
        $this->assertEquals(
            2,
            $this->value->get(),
            'Ожидается, что по умолчанию будет получено локальное значение.'
        );

        $this->value->set(42);

        $this->assertEquals(
            42,
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что будет получено local сессионное значение.'
        );
        $this->assertEquals(
            1,
            $this->value->get(IConfigValue::KEY_MASTER),
            'Ожидается, что будет получено master сессионное значение.'
        );

        $this->value->set(4);
        $this->assertEquals(
            4,
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что будет получено session значение.'
        );
    }

    public function testReset()
    {
        $this->value->set(42);
        $this->value->reset();

        $this->assertEquals(
            2,
            $this->value->get(),
            'Ожидается, что сессионное значение будет сброшено.'
        );
    }

    /**
     * Тест удаления значения.
     */
    public function testDelete()
    {
        $this->value->set(42);
        $this->value->del();

        $this->assertEquals(
            1,
            $this->value->get(),
            'Ожидается, что будет получено master значение.'
        );
        $this->assertTrue($this->value->has());

        $this->assertFalse(
            $this->value->has(IConfigValue::KEY_LOCAL),
            'Ожидается, что сессионное значение было удалено.'
        );
        $this->assertNull(
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что сессионное значение было удалено.'
        );
    }

    public function testDeleteWithoutMasterValue()
    {
        $this->value = new ConfigValue();

        $this->value->set(12, IConfigValue::KEY_LOCAL);
        $this->value->del();

        $this->assertNull(
            $this->value->get(),
            'Ожидается, что master значение не существует.'
        );
        $this->assertFalse(
            $this->value->has(),
            'Ожидается, что local значение не существует.'
        );
    }

    public function testSerialize()
    {
        $this->value->set(42);

        $this->value = unserialize(serialize($this->value));

        $this->assertEquals(
            2,
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что local значение было восстановлено.'
        );
        $this->assertEquals(
            1,
            $this->value->get(IConfigValue::KEY_MASTER),
            'Ожидается, что master значение было восстановлено.'
        );

    }

    public function testSave()
    {
        $this->value
            ->set(42, IConfigValue::KEY_LOCAL)
            ->set(43, IConfigValue::KEY_MASTER)
            ->save();

        $this->value = unserialize(serialize($this->value));

        $this->assertEquals(
            42,
            $this->value->get(IConfigValue::KEY_LOCAL),
            'Ожидается, что local значение было сохранено.'
        );
        $this->assertEquals(
            43,
            $this->value->get(IConfigValue::KEY_MASTER),
            'Ожидается, что master значение было сохранено.'
        );
    }
}
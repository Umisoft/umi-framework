<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\type;

use umi\filter\IFilter;
use umi\filter\type\Null;
use utest\TestCase;

/**
 * Класс тестирование фильтра Null
 */
class NullFilterTests extends TestCase
{

    /**
     * @var IFilter $filter фильтр
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new Null();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(1, $this->filter->filter(1), "Ожидается, что значение не будет изменено");
        $this->assertEquals('string', $this->filter->filter('string'), "Ожидается, что значение не будет изменено");

        $this->assertNull($this->filter->filter(0), "Ожидается, что отфильтрованное значение будет null");
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new Null(['optional_values' => ['null', 'false']]);
        $this->assertEquals('string', $filter->filter('string'), "Ожидается, что значение не будет изменено");

        $this->assertNull($filter->filter('false'), "Ожидается, что отфильтрованное значение будет null");
    }
}
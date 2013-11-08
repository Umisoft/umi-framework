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
use umi\filter\type\StringTrim;
use utest\TestCase;

/**
 * Класс тестирование фильтра StringTrim
 */
class StringTrimFilterTests extends TestCase
{
    /**
     * @var IFilter $filter фильтр
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new StringTrim();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            "test string",
            $this->filter->filter("   test string   "),
            "Ожидается, что пробелы будут удалены"
        );
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new StringTrim(['charlist' => '/']);

        $this->assertEquals(
            "base/string",
            $filter->filter("/base/string/"),
            "Ожидается, что символ / будет удален по краям строки"
        );
    }
}
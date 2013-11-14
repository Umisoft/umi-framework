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
use umi\filter\type\StringToLower;
use utest\filter\FilterTestCase;

/**
 * Класс тестирование фильтра StringToLower
 */
class StringToLowerFilterTests extends FilterTestCase
{
    /**
     * @var IFilter $filter фильтр
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new StringToLower();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            "test string",
            $this->filter->filter("Test String"),
            "Ожидается, что строка будет приведена к нижнему регистру"
        );
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new StringToLower(['encoding' => 'utf-8']);
        $this->assertEquals(
            "тестовая строка",
            $filter->filter("Тестовая Строка"),
            "Ожидается, что строка будет приведена к нижнему регистру"
        );
    }
}
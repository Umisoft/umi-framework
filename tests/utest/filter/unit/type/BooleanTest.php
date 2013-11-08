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
use umi\filter\type\Boolean;
use utest\TestCase;

/**
 * Класс тестирование фильтра Boolean
 */
class BooleanFilterTests extends TestCase
{

    /**
     * @var IFilter $filter
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new Boolean();
    }

    public function testFilterBaseUsage()
    {
        $this->assertTrue($this->filter->filter(1), "Ожидается, что отфильтрованное значение будет true");
        $this->assertFalse($this->filter->filter(0), "Ожидается, что отфильтрованное значение будет false");

        $this->assertTrue(
            $this->filter->filter('not empty string'),
            "Ожидается, что отфильтрованное значение будет true"
        );
        $this->assertFalse($this->filter->filter(''), "Ожидается, что отфильтрованное значение будет false");
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new Boolean([
            'optional_values' => [
                'yes' => 1,
                'no'  => 0
            ]
        ]);
        $this->assertTrue($filter->filter('yes'), "Ожидается, что отфильтрованное значение будет true");
        $this->assertFalse($filter->filter('no'), "Ожидается, что отфильтрованное значение будет false");
    }
}
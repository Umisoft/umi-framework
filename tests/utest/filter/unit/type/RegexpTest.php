<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\type;

use umi\filter\exception\RuntimeException;
use umi\filter\IFilter;
use umi\filter\type\Regexp;
use utest\TestCase;

/**
 * Класс тестирование фильтра Regexp
 */
class RegexpFilterTests extends TestCase
{

    /**
     * @var IFilter $filter фильтр
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new Regexp([
            'pattern'     => '/[0-9]+/',
            'replacement' => 'number',
        ]);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongPattern()
    {
        new Regexp([]);
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            "string with number",
            $this->filter->filter('string with 1234'),
            "Ожидается, что значение будет изменено."
        );
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new Regexp([
            'pattern'     => '/[0-9]+/',
            'replacement' => 'number',
            'limit'       => 1,
        ]);

        $this->assertEquals(
            "string with number = 42",
            $filter->filter('string with 1234 = 42'),
            "Ожидается, что только одно значение будет изменено."
        );
    }
}
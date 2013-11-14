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
use umi\filter\type\StripTags;
use utest\filter\FilterTestCase;

/**
 * Класс тестирование фильтра StripTags
 */
class StripTagsFilterTests extends FilterTestCase
{

    /**
     * @var IFilter $filter
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new StripTags();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            "string with tag",
            $this->filter->filter("string with <strong>tag</strong>"),
            "Ожидается, что тэг strong будет удален"
        );
    }

    public function testFilterAdvancedUsage()
    {
        $filter = new StripTags([
            'allowed' => ['b', 'strong']
        ]);

        $this->assertEquals(
            "string with <b>tag</b> text",
            $filter->filter("string with <b>tag</b> <i>text</i>"),
            "Ожидается, что тэг b не будет удален"
        );
    }
}
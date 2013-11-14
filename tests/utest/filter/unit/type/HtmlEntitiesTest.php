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
use umi\filter\type\HtmlEntities;
use utest\filter\FilterTestCase;

/**
 * Класс тестирование фильтра HtmlEntities
 */
class HtmlEntitiesFilterTests extends FilterTestCase
{
    /**
     * @var IFilter $filter
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new HtmlEntities();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            'html &amp; css',
            $this->filter->filter('html & css'),
            "Ожидается, что строка будет заэкранирована"
        );
    }
}
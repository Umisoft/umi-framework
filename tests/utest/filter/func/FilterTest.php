<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\func;

use umi\filter\IFilter;
use umi\filter\IFilterFactory;
use utest\filter\FilterTestCase;

/**
 * Тестирование фильтров
 */
class FilterTest extends FilterTestCase
{

    /**
     * @var IFilterFactory $filterFactory
     */
    protected $filterFactory;

    /**
     * Создание инструментария фильтрации данных
     */
    public function setUpFixtures()
    {
        $this->filterFactory = $this->getTestToolkit()->getService('umi\filter\IFilterFactory');
    }

    /**
     * Тестирование работы одного фильтра
     */
    public function testSingleFilter()
    {
        $filter = $this->filterFactory
            ->createFilter(IFilterFactory::TYPE_STRING_TRIM);

        $this->assertEquals(
            "string",
            $filter->filter("   string   "),
            "Ожидается, что пробелы будут удалены из строки"
        );
    }

    /**
     * Тестирование работы коллекции валидаторов
     */
    public function testMultipleFilter()
    {

        $filter = $this->filterFactory
            ->createFilterCollection(
            [
                IFilterFactory::TYPE_STRING_TRIM => [],
                IFilterFactory::TYPE_NULL        => []
            ]
        );

        $this->assertNull($filter->filter("    "), "Ожидаются преобразования: '    ' -> '' -> NULL");
    }

    /**
     * Проверка правильной установки опций, при создании коллекции валидаторов
     */
    public function testMultipleFilterOptions()
    {
        /**
         * @var IFilter $filter
         */
        $filter = $this->filterFactory
            ->createFilterCollection(
            [
                IFilterFactory::TYPE_STRING_TRIM => [],
                IFilterFactory::TYPE_REGEXP      => [
                    'pattern' => '/[0-9]+/',
                    'replacement' => '{number}'
                ]
            ]
        );

        $this->assertEquals(
            "{number}+{number}={number}",
            $filter->filter("  1+2=3   "),
            "Ожидается, что оба фильтра отработают"
        );
    }
}
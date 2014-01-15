<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\head;

use umi\templating\extension\helper\type\head\title\TitleCollection;
use umi\templating\extension\helper\type\head\title\TitleHelper;
use utest\templating\TemplatingTestCase;

/**
 * Тест помощника заголовка страницы
 */
class TitleHelperTest extends TemplatingTestCase
{
    /**
     * @var TitleHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new TitleHelper();
    }

    public function testBasic()
    {
        $helper = $this->helper;
        /**
         * @var TitleCollection $collection
         */
        $collection = $helper();

        $this->assertEmpty(
            (string) $collection,
            'Ожидается, что заголовок страницы выведен не будет.'
        );

        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\head\title\TitleCollection',
            $collection,
            'Ожидается, что будет получена коллекция заголовка.'
        );

        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\head\title\TitleCollection',
            $collection->setTitle('Test Title')
        );

        $this->assertEquals(
            '<title>Test Title</title>',
            (string) $collection
        );

    }
}
 
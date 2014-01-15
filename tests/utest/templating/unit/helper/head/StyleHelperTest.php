<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\head;

use umi\templating\extension\helper\type\head\style\StyleCollection;
use umi\templating\extension\helper\type\head\style\StyleHelper;
use utest\templating\TemplatingTestCase;

/**
 * Тесты помощника вида скриптов.
 */
class StyleHelperTest extends TemplatingTestCase
{
    /**
     * @var StyleHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new StyleHelper();
    }

    public function testBasic()
    {
        $helper = $this->helper;
        /**
         * @var StyleCollection $collection
         */
        $collection = $helper();

        $this->assertEmpty(
            (string) $collection,
            'Ожидается, что стилей выведенно не будет.'
        );

        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\head\style\StyleCollection',
            $collection,
            'Ожидается, что будет получена коллекция скриптов.'
        );
    }

    public function testFiles()
    {
        $helper = $this->helper;
        /**
         * @var StyleCollection $collection
         */
        $collection = $helper();

        $this->assertSame(
            $collection,
            $collection->appendFile('/style.css', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<link rel="stylesheet" type="text/mytype" href="/style.css" />';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что стили будут добавлены.'
        );

        $this->assertSame(
            $collection,
            $collection->prependFile('/style2.css', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<link rel="stylesheet" type="text/mytype" href="/style2.css" />' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что стили будут добавлены в начало.'
        );
    }

    public function testContent()
    {
        $helper = $this->helper;
        /**
         * @var StyleCollection $collection
         */
        $collection = $helper();

        $scripts = (string) $collection;

        $this->assertSame(
            $collection,
            $collection->appendStyle('content', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts .= '<style type="text/mytype">content</style>';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что стили будут добавлены в конец.'
        );

        $this->assertSame(
            $collection,
            $collection->prependStyle('content2', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<style type="text/mytype">content2</style>' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что стили будут добавлены в начало.'
        );
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\head;

use umi\templating\extension\helper\type\head\meta\MetaCollection;
use umi\templating\extension\helper\type\head\meta\MetaHelper;
use utest\TestCase;

/**
 * Тесты помошника вида скриптов.
 */
class MetaHelperTest extends TestCase
{
    /**
     * @var MetaHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new MetaHelper();
    }

    public function testBasic()
    {
        $helper = $this->helper;
        /**
         * @var MetaCollection $collection
         */
        $collection = $helper();

        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\head\meta\MetaCollection',
            $collection,
            'Ожидается, что будет получена коллекция мета информации.'
        );

        $this->assertEmpty(
            (string) $collection,
            'Ожидается, что скриптов выведенно не будет.'
        );

        $collection->setCharset('utf-8');

        $this->assertEquals(
            '<meta charset="utf-8" />',
            (string) $collection,
            'Ожидается, что мета иформация о кодировке будет выставлена.'
        );
    }

    public function testHttpEquiv()
    {
        $helper = $this->helper;
        /**
         * @var MetaCollection $collection
         */
        $collection = $helper();

        $this->assertSame(
            $collection,
            $collection->appendHttpEquiv('httpEquiv', 'value1'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<meta http-equiv="httpEquiv" content="value1" />';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что мета информация будет добавлена.'
        );

        $this->assertSame(
            $collection,
            $collection->prependHttpEquiv('httpEquiv2', 'value2'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<meta http-equiv="httpEquiv2" content="value2" />' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что мета информация будет добавлена в начало.'
        );
    }

    public function testContent()
    {
        $helper = $this->helper;
        /**
         * @var MetaCollection $collection
         */
        $collection = $helper();

        $scripts = (string) $collection;

        $this->assertSame(
            $collection,
            $collection->appendName('name1', 'value1'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts .= '<meta name="name1" content="value1" />';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен в конец.'
        );

        $this->assertSame(
            $collection,
            $collection->prependName('name2', 'value2'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<meta name="name2" content="value2" />' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен в начало.'
        );
    }
}
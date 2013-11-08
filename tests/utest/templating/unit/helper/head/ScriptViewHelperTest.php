<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\head;

use umi\templating\extension\helper\type\head\script\ScriptCollection;
use umi\templating\extension\helper\type\head\script\ScriptHelper;
use utest\TestCase;

/**
 * Тесты помошника вида скриптов.
 */
class ScriptHelperTest extends TestCase
{
    /**
     * @var ScriptHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new ScriptHelper();
    }

    public function testBasic()
    {
        $helper = $this->helper;
        /**
         * @var ScriptCollection $collection
         */
        $collection = $helper();

        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\head\script\ScriptCollection',
            $collection,
            'Ожидается, что будет получена коллекция скриптов.'
        );

        $this->assertEmpty(
            (string) $collection,
            'Ожидается, что скриптов выведенно не будет.'
        );
    }

    public function testFiles()
    {
        $helper = $this->helper;
        /**
         * @var ScriptCollection $collection
         */
        $collection = $helper();

        $this->assertSame(
            $collection,
            $collection->appendFile('/script.js', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<script type="text/mytype" src="/script.js"></script>';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен.'
        );

        $this->assertSame(
            $collection,
            $collection->prependFile('/script2.js', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<script type="text/mytype" src="/script2.js"></script>' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен в начало.'
        );
    }

    public function testContent()
    {
        $helper = $this->helper;
        /**
         * @var ScriptCollection $collection
         */
        $collection = $helper();

        $scripts = (string) $collection;

        $this->assertSame(
            $collection,
            $collection->appendScript('content', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts .= '<script type="text/mytype">content</script>';

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен в конец.'
        );

        $this->assertSame(
            $collection,
            $collection->prependScript('content2', 'text/mytype'),
            'Ожидается, что будет возвращен $this.'
        );

        $scripts = '<script type="text/mytype">content2</script>' . $scripts;

        $this->assertEquals(
            $scripts,
            (string) $collection,
            'Ожидается, что скрипт будет добавлен в начало.'
        );
    }
}
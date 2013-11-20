<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\response;

use umi\http\response\header\HeaderCollection;
use umi\http\response\header\IHeaderCollection;
use utest\http\HttpTestCase;

/**
 * Class HeaderCollectionTest
 */
class HeaderCollectionTest extends HttpTestCase
{
    /**
     * @var IHeaderCollection $headers
     */
    private $headers;

    public function setUpFixtures()
    {
        $this->headers = new HeaderCollection();
        $this->resolveOptionalDependencies($this->headers);
    }

    public function testHeaders()
    {
        $this->assertEmpty($this->headers->getHeaders(), 'Ожидается, что в новом ответе нет заголовков.');

        $this->assertSame($this->headers, $this->headers->setHeader('X-Powered-By', 'UMI.CMS'));

        $this->assertEquals(
            ['X-Powered-By' => 'UMI.CMS'],
            $this->headers->getHeaders(),
            'Ожидается, что будет получен установленный заголовок.'
        );
    }

    public function testCookies()
    {
        $this->assertEmpty($this->headers->getCookies(), 'Ожидается, что в новом ответе нет cookies.');
        $this->assertSame(
            $this->headers,
            $this->headers->setCookie('test', 'value', ['expire' => 30])
        );
        $this->assertEquals(
            [
                'test' => [
                    'value'    => 'value',
                    'expire'   => 30,
                    'path'     => null,
                    'domain'   => null,
                    'secure'   => false,
                    'httponly' => false
                ]
            ],
            $this->headers->getCookies(),
            'Ожидается, что cookie будет установлен.'
        );
    }

    public function testSend()
    {
        /*
        $this->headers->setCookie('test', 'cookie');
        $this->headers->send();

        $this->assertEquals([], xdebug_get_headers());
        */
    }

}

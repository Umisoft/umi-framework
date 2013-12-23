<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\request;

use umi\http\request\IRequest;
use umi\http\request\Request;
use utest\http\HttpTestCase;

/**
 * Класс RequestTest
 */
class RequestTest extends HttpTestCase
{
    /**
     * @var IRequest $request
     */
    protected $request = null;
    /**
     * @var array $server
     */
    private $server;

    function setUpFixtures()
    {
        $this->server = $_SERVER;
        $this->request = new Request();
        $this->resolveOptionalDependencies($this->request);
    }

    function tearDownFixtures()
    {
        $_SERVER = $this->server;
        $_POST = [];
        $_GET = [];
        $_REQUEST = [];
        $_COOKIE = [];
        $_FILES = [];
    }

    /**
     * Базовые тесты.
     */
    public function testBasic()
    {
        $this->assertEquals(
            IRequest::METHOD_CLI,
            $this->request->getMethod(),
            'Ожидается, что при консольном вызове REQUEST_METHOD будет CLI'
        );
        $this->assertEquals(
            'cli',
            $this->request->getScheme(),
            'Ожидается, что при консольном вызове REQUEST_PROTOCOL будет CLI'
        );
        $this->assertEmpty(
            $this->request->getContent(),
            'Ожидается, что при консольном вызове тело запроса будет пустым'
        );
        $this->assertNull(
            $this->request->getVersion(),
            'Ожидается, что при консольном вызове версии REQUEST_METHOD не будет'
        );

        $_SERVER['HTTPS'] = 'On';
        $this->assertEquals('https', $this->request->getScheme(), 'Ожидается, что будет определен HTTPS');

        $this->request->getParams(IRequest::HEADERS)
            ->set('REQUEST_URI', '/example/page');
        $this->assertEquals(
            '/example/page',
            $this->request->getRequestURI(),
            'Ожиадается, что Request URI будет установлен.'
        );

        $this->request->getParams(IRequest::HEADERS)
            ->set('HTTP_HOST', 'example.com');
        $this->assertEquals('example.com', $this->request->getHost(), 'Ожиадается, что Request URI будет установлен.');

        $this->assertEquals('https://example.com', $this->request->getHostURI());

    }

    public function testParamCollections()
    {
        $_COOKIE['test'] = 'test';
        $this->assertEquals('test', $this->request->getVar(IRequest::COOKIE, 'test'));

        $_GET['test'] = 'test';
        $this->assertEquals('test', $this->request->getVar(IRequest::GET, 'test'));

        $_POST['test'] = 'test';
        $this->assertEquals('test', $this->request->getVar(IRequest::POST, 'test'));

        $_SERVER['test'] = 'test';
        $this->assertEquals('test', $this->request->getVar(IRequest::HEADERS, 'test'));

        $_FILES['test'] = [
            'filename' => 'test',
        ];
        $this->assertEquals($_FILES['test'], $this->request->getVar(IRequest::FILES, 'test'));

        $this->request->getParams('example')
            ->setArray(
            [
                'route' => '123'
            ]
        );
        $this->assertEquals(
            '123',
            $this->request->getParams('example')
                ->get('route')
        );
    }

    /**
     * Тесты реферера.
     */
    public function testReferer()
    {
        $this->assertNull($this->request->getReferer(), 'Ожидается, что Referer будет пустым.');

        $this->request->getParams(IRequest::HEADERS)
            ->set('SERVER_PROTOCOL', 'http');
        $this->request->getParams(IRequest::HEADERS)
            ->set('HTTP_HOST', 'example.com');
        $this->request->getParams(IRequest::HEADERS)
            ->set('HTTP_REFERER', 'http://example.com/page');

        $this->assertEquals(
            '/page',
            $this->request->getReferer(),
            'Ожидается, что внутренний Referer будет без хоста и протокола.'
        );

        $this->request->getParams(IRequest::HEADERS)
            ->set('HTTP_REFERER', 'http://mysite.com/page');
        $this->assertEquals(
            'http://mysite.com/page',
            $this->request->getReferer(),
            'Ожидается, что внешний Referer будет с хостом и протоколом.'
        );
    }
}
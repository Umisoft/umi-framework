<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\response;

use umi\http\response\IResponse;
use umi\http\response\Response;
use utest\http\HttpTestCase;

/**
 * Класс RequestTest
 */
class ResponseTest extends HttpTestCase
{

    /**
     * @var IResponse $response
     */
    public $response;

    public function setUpFixtures()
    {
        $this->response = new Response();
        $this->resolveOptionalDependencies($this->response);
    }

    public function testContent()
    {
        $this->assertEmpty($this->response->getContent(), 'Ожидается, что в новом ответе нет содержания.');
        $this->assertInstanceOf(
            'umi\http\response\IResponse',
            $this->response->setContent('Hello world!'),
            'Ожидается, что будет возвращен $this'
        );
        $this->assertEquals(
            'Hello world!',
            $this->response->getContent(),
            'Ожидается, что будет получен установленное содержание.'
        );
    }

    public function testCode()
    {
        $this->assertEquals(200, $this->response->getCode(), 'Ожидается код ответа по умолчанию - 200.');

        $this->assertInstanceOf(
            'umi\http\response\IResponse',
            $this->response->setCode(404),
            'Ожидается, что будет возвращен $this'
        );
        $this->assertEquals(404, $this->response->getCode(), 'Ожидается код ответа будет установлен.');
    }

    public function testResponseCodeRestore()
    {
        $this->response->setCode(400);

        $e = null;
        try {
            $this->response->send();
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'Ожидается, что исключение будет брошено.');
        $this->assertEquals(200, http_response_code());
    }
}
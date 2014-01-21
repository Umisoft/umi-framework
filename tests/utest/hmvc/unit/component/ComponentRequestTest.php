<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\component;

use umi\hmvc\dispatcher\http\HTTPComponentRequest;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use utest\hmvc\HMVCTestCase;

/**
 * Class HTTPComponentRequest
 */
class ComponentRequestTest extends HMVCTestCase
{
    /**
     * @var IHTTPComponentRequest $request
     */
    private $request;

    public function setUpFixtures()
    {
        $this->request = new HTTPComponentRequest('uri');
        $this->resolveOptionalDependencies($this->request);
    }

    public function testRequestUri()
    {
        $this->assertEquals('uri', $this->request->getRequestURI());
        $this->assertNotEquals('uri', $this->request->getVar(IHTTPComponentRequest::HEADERS, 'REQUEST_URI'));
    }

    public function testRouteParams()
    {
        $this->assertSame(
            $this->request,
            $this->request->setRouteParams(['params'])
        );

        $this->assertEquals(
            ['params'],
            $this->request->getParams(IHTTPComponentRequest::ROUTE)
                ->toArray()
        );
    }
}
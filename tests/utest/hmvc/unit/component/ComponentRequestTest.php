<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\component;

use umi\hmvc\component\request\ComponentRequest;
use umi\hmvc\component\request\IComponentRequest;
use utest\TestCase;

/**
 * Class ComponentRequest
 */
class ComponentRequestTest extends TestCase
{
    /**
     * @var IComponentRequest $request
     */
    private $request;

    public function setUpFixtures()
    {
        $this->request = new ComponentRequest('uri');
        $this->resolveOptionalDependencies($this->request);
    }

    public function testRequestUri()
    {
        $this->assertEquals('uri', $this->request->getRequestUri());
        $this->assertNotEquals('uri', $this->request->getVar(IComponentRequest::HEADERS, 'REQUEST_URI'));
    }

    public function testRouteParams()
    {
        $this->assertSame(
            $this->request,
            $this->request->setRouteParams(['params'])
        );

        $this->assertEquals(
            ['params'],
            $this->request->getParams(IComponentRequest::ROUTE)
                ->toArray()
        );
    }
}
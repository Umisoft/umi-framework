<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\controller;

use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\controller\BaseController;

/**
 * Mock controller.
 */
class MockController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        $data = $request->getVar(IHTTPComponentRequest::ROUTE, 'data', 'mock');

        return $this->createPlainResponse($data);
    }
}
 
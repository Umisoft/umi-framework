<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\controller;

use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\controller\type\BaseController;

/**
 * Class MockRenderController
 */
class MockRenderController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        $data = $request->getVar(IHTTPComponentRequest::ROUTE, 'data', 'mock');

        return $this->createDisplayResponse(
            'result',
            [
                'data' => $data
            ]
        );
    }
}
 
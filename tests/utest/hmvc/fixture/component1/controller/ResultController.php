<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\fixture\component1\controller;

use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\controller\type\BaseController;

/**
 * Class ResultController
 */
class ResultController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        $ex = $request->getVar(IHTTPComponentRequest::ROUTE, 'route');

        return $this->createDisplayResponse(
            'result',
            [
                'var'    => $ex,
                'sample' => 'Hello world',
            ]
        );
    }
}
 
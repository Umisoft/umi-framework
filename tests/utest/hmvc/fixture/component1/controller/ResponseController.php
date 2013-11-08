<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\fixture\component1\controller;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\controller\type\BaseController;

/**
 * Class ExampleController
 */
class ResponseController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(IComponentRequest $request)
    {
        $ex = $request->getVar(IComponentRequest::ROUTE, 'route');

        return $this->createComponentResponse()
            ->setContent($ex);
    }
}

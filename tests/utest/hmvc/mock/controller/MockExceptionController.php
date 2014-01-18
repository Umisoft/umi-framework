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
use umi\hmvc\exception\http\HttpException;

/**
 * Mock класс контроллера бросающего исключения
 */
class MockExceptionController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        throw new HttpException(401, 'Http exception thrown.');
    }
}
 
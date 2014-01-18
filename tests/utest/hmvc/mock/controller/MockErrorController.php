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
 * Class MockErrorController
 */
class MockErrorController extends BaseController
{
    /**
     * @var \Exception $exception
     */
    protected $exception;

    /**
     * Конструктор.
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        return $this->createPlainResponse($this->exception->getMessage(), $this->exception->getCode());
    }
}
 
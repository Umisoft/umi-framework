<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\hmvc\mock\view\helper;

use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextAware;
use utest\hmvc\mock\model\MockBaseModel;

/**
 * Class MockContextModelHelper
 */
class MockContextModelHelper implements IContextAware
{
    use TContextAware;

    /**
     * @var MockBaseModel $model
     */
    protected $model;

    public function __construct(MockBaseModel $model)
    {
        $this->model = $model;
    }

    public function __invoke()
    {
        $requestURI = $this->getContext()
            ->getRequest()->getRequestURI();

        return 'URI: ' . $requestURI . '. Model: ' . $this->model->getVariable();
    }
}

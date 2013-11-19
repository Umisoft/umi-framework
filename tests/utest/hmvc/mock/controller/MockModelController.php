<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\controller;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\controller\type\BaseController;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\TModelAware;
use utest\hmvc\mock\model\MockBaseModel;

/**
 * Mock класс контроллера для тестирования внедрения моделей в конструктор.
 */
class MockModelController extends BaseController implements IModelAware
{
    use TModelAware;

    /**
     * @var MockBaseModel $model модель
     */
    private $model;

    /**
     * Конструктор.
     * @param MockBaseModel $model
     */
    public function __construct(MockBaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(IComponentRequest $request)
    {
        /**
         * @var MockBaseModel $model2 модель
         */
        $model2 = $this->createModelByName('mock');

        return $this->createResponse($this->model->getVariable() . $model2->getVariable());
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\model;

/**
 * Модель с внедрением зависимости через конструктор.
 */
class MockDependencyModel
{
    private $baseModel;

    public function __construct(MockBaseModel $model)
    {
        $this->baseModel = $model;
    }

    public function getVariable()
    {
        return 'dependency ' . $this->baseModel->getVariable();
    }
}
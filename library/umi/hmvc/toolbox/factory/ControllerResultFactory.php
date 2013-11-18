<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\controller\result\IControllerResultFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика результатов работы контроллера.
 */
class ControllerResultFactory implements IControllerResultFactory, IFactory
{
    use TFactory;

    /**
     * @var string $controllerResultClass класс результата работы контроллера
     */
    public $controllerResultClass = 'umi\hmvc\controller\result\ControllerResult';

    /**
     * {@inheritdoc}
     */
    public function createControllerResult($template, array $variables = [])
    {
        return $this->getPrototype(
                $this->controllerResultClass,
                ['umi\hmvc\controller\result\IControllerResult']
            )
            ->createInstance([$template, $variables]);
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\component\response\IComponentResponseFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для результатов работы MVC компонента.
 */
class ComponentResponseFactory implements IComponentResponseFactory, IFactory
{
    use TFactory;

    /**
     * @var string $componentResponseClass класс результата работы компонента
     */
    public $componentResponseClass = 'umi\hmvc\component\response\ComponentResponse';

    /**
     * {@inheritdoc}
     */
    public function createComponentResponse()
    {
        return $this->getPrototype(
                $this->componentResponseClass,
                ['umi\hmvc\component\response\IComponentResponse']
            )
            ->createInstance();
    }
}
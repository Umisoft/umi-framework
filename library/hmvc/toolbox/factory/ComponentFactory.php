<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\component\IComponentFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика HMVC компонентов.
 */
class ComponentFactory implements IComponentFactory, IFactory
{
    use TFactory;

    /**
     * @var string $componentClass MVC компонент по умолчанию
     */
    public $componentClass = 'umi\hmvc\component\Component';

    /**
     * {@inheritdoc}
     */
    public function createComponent(array $options)
    {
        $componentClass = isset($options['componentClass']) ? $options['componentClass'] : $this->componentClass;
        unset($options['componentClass']);

        return $this->getPrototype(
                $componentClass,
                ['umi\hmvc\component\IComponent']
            )
            ->createInstance([$options]);
    }

}
 
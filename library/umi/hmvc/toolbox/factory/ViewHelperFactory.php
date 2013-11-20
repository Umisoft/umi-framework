<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextAware;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\templating\toolbox\factory\HelperFactory;
use umi\toolkit\prototype\IPrototype;

/**
 * Фабрика помощников вида для компонента.
 */
class ViewHelperFactory extends HelperFactory implements IModelAware, IContextAware
{
    use TContextAware;

    /**
     * @var IModelFactory $modelFactory фабрика для создания моделей
     */
    protected $modelFactory;

    /**
     * {@inheritdoc}
     */
    public function setModelFactory(IModelFactory $factory)
    {
        $this->modelFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createHelper($class)
    {
        $helper = parent::createHelper($class);

        if ($helper instanceof IModelAware && $this->modelFactory) {
            $helper->setModelFactory($this->modelFactory);
        }

        if ($helper instanceof IContextAware) {
            $helper->clearContext();

            if ($this->hasContext()) {
                $helper->setContext($this->getContext());
            }
        }

        return $helper;
    }

    /**
     * {@inheritdoc}
     */
    protected function initPrototype(IPrototype $prototype)
    {
        $prototype->registerConstructorDependency(
            'umi\hmvc\model\IModel',
            function ($concreteClassName) {
                if ($this->modelFactory instanceof IModelFactory) {
                    return $this->modelFactory->createByClass($concreteClassName);
                }

                return null;
            }
        );
    }
}

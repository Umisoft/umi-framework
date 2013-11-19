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
use umi\hmvc\view\extension\IViewExtensionFactory;
use umi\templating\extension\helper\collection\IHelperCollection;
use umi\templating\extension\helper\IHelperFactory;
use umi\templating\extension\helper\IHelperFactoryAware;
use umi\templating\toolbox\factory\ExtensionFactory;

/**
 * Фабрика расширений для слоя отображения.
 */
class ViewExtensionFactory extends ExtensionFactory implements IViewExtensionFactory, IContextAware, IModelAware
{
    use TContextAware;

    /**
     * @var string $viewHelperCollectionClass класс коллекции помощников вида
     */
    public $viewHelperCollectionClass = 'umi\hmvc\view\extension\helper\ViewHelperCollection';
    /**
     * @var array $viewHelperCollection стандартные помощники вида
     */
    public $viewHelperCollection = [
        'url' => 'umi\hmvc\view\helper\UrlHelper'
    ];
    /**
     * @var string $viewHelperFactoryClass фабрика помощников вида
     */
    public $viewHelperFactoryClass = 'umi\hmvc\toolbox\factory\ViewHelperFactory';
    /**
     * @var IHelperCollection $defaultViewHelperCollection
     */
    private $defaultViewHelperCollection;
    /**
     * @var IModelFactory $modelFactory фабрика для создания моделей
     */
    private $modelFactory;

    /**
     * {@inheritdoc}
     */
    public function setModelFactory(IModelFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createViewHelperCollection()
    {
        $collection = $this->newViewHelperCollectionInstance();

        if ($collection instanceof IModelAware && $this->modelFactory) {
            $collection->setModelFactory($this->modelFactory);
        }

        $this->injectContext($collection);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultViewHelperCollection()
    {
        if (!$this->defaultViewHelperCollection) {
            $this->defaultViewHelperCollection = $this->newViewHelperCollectionInstance()
                ->addHelpers($this->viewHelperCollection);
        }

        $this->injectContext($this->defaultViewHelperCollection);

        return $this->defaultViewHelperCollection;
    }

    /**
     * Возвращает фабрику помощников вида.
     * @return IHelperFactory
     */
    protected function getViewHelperFactory()
    {
        $factory = $this->createInstance(
            $this->viewHelperFactoryClass,
            [],
            ['umi\templating\extension\helper\IHelperFactory']
        );

        if ($factory instanceof IModelAware && $this->modelFactory) {
            $factory->setModelFactory($this->modelFactory);
        }

        return $factory;
    }

    /**
     * Создает новую коллекцию помощников вида.
     * @return IHelperCollection
     */
    private function newViewHelperCollectionInstance()
    {
        $viewHelperCollection = $this->createInstance(
            $this->viewHelperCollectionClass,
            [],
            ['umi\templating\extension\helper\collection\IHelperCollection']
        );

        if ($viewHelperCollection instanceof IHelperFactoryAware) {
            $viewHelperCollection->setTemplatingHelperFactory($this->getViewHelperFactory());
        }

        return $viewHelperCollection;
    }

    /**
     * Внедряет контекст в объект.
     * @param object $object
     */
    private function injectContext($object)
    {
        if ($object instanceof IContextAware) {
            if ($this->hasContext()) {
                $object->setContext($this->getContext());
            } else {
                $object->clearContext();
            }
        }
    }
}

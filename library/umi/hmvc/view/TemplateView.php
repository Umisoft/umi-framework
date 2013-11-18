<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view;

use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextInjectorAware;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\extension\IViewExtensionFactory;
use umi\hmvc\view\extension\IViewExtensionFactoryAware;
use umi\hmvc\view\extension\TViewExtensionFactoryAware;
use umi\templating\engine\ITemplateEngine;
use umi\templating\engine\ITemplateEngineAware;
use umi\templating\engine\TTemplateEngineAware;
use umi\templating\extension\adapter\IExtensionAdapterAware;
use umi\templating\extension\adapter\TExtensionAdapterAware;

/**
 * Слой отображения.
 */
class TemplateView implements IView,
    ITemplateEngineAware, IExtensionAdapterAware, IViewExtensionFactoryAware,
    IModelAware, IContextAware
{
    use TTemplateEngineAware;
    use TExtensionAdapterAware;
    use TViewExtensionFactoryAware;
    use TContextInjectorAware;

    /** Опция для установки помощников вида  */
    const OPTION_HELPERS = 'helpers';

    /**
     * @var array $options опции
     */
    private $options;
    /**
     * @var ITemplateEngine $templateEngine шаблонизатор
     */
    private $templateEngine;
    /**
     * @var IModelFactory $modelFactory фабрика моделей
     */
    private $modelFactory;

    /**
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

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
    public function render($template, array $params = [])
    {
        return $this->getTemplateEngine()
            ->render($template, $params);
    }

    /**
     * Внедряет зависимости от контекста в фабрику расширений
     * шаблонизатора.
     * @param IViewExtensionFactory $factory
     */
    protected function injectContextToViewExtensionFactory(IViewExtensionFactory $factory)
    {
        $this->injectContext($factory);

        if ($factory instanceof IModelAware && $this->modelFactory) {
            $factory->setModelFactory($this->modelFactory);
        }
    }

    /**
     * Возвращает шаблонизатор созданный на основе опций.
     * @return ITemplateEngine шаблонизатор
     */
    protected function getTemplateEngine()
    {
        if (!$this->templateEngine) {
            $this->setupTemplateEngine();
        }

        return $this->templateEngine;
    }

    /**
     * Создает и инициализирует шаблонизатор для компонента.
     */
    private function setupTemplateEngine()
    {
        $templateEngineType = isset($this->options[self::OPTION_TYPE]) ? $this->options[self::OPTION_TYPE] : null;
        $this->templateEngine = $this->createTemplateEngine($templateEngineType, $this->options);
        $this->setupExtensionAdapter();
    }

    /**
     * Создает адаптер расширения для компонента. Устанавливает адаптер в шаблонизатор.
     */
    private function setupExtensionAdapter()
    {
        $extension = $this->createTemplatingExtensionAdapter();

        $extension->addHelperCollection('template', $this->getDefaultTemplateHelperCollection());
        $extension->addHelperCollection('view', $this->getDefaultViewHelperCollection());

        if (isset($this->options[self::OPTION_HELPERS])) {
            $viewHelperCollection = $this->createViewHelperCollection();
            $viewHelperCollection->addHelpers($this->options[self::OPTION_HELPERS]);

            $extension->addHelperCollection('component', $viewHelperCollection);
        }

        $this->templateEngine->setExtensionAdapter($extension);
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox;

use umi\templating\engine\ITemplateEngineAware;
use umi\templating\extension\adapter\IExtensionAdapterAware;
use umi\templating\extension\IExtensionFactoryAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для работы с шаблонизаторами.
 */
class TemplatingTools implements ITemplatingTools
{

    /** Имя набора инструментов */
    const NAME = 'templating';

    use TToolbox;

    /**
     * @var string $templatingFactoryClass класс фабрики шаблонизаторов
     */
    public $templateEngineFactoryClass = 'umi\templating\toolbox\factory\TemplateEngineFactory';
    /**
     * @var string $extensionFactoryClass класс фабрики для создания расширений шаблонизатора
     */
    public $extensionFactoryClass = 'umi\templating\toolbox\factory\ExtensionFactory';
    /**
     * @var string $extensionAdapterFactoryClass класс фабрики для создания адаптеров для расширения шаблонизатора
     */
    public $extensionAdapterFactoryClass = 'umi\templating\toolbox\factory\ExtensionAdapterFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'engine',
            $this->templateEngineFactoryClass,
            ['umi\templating\engine\ITemplateEngineFactory']
        );

        $this->registerFactory(
            'extension',
            $this->extensionFactoryClass,
            ['umi\templating\extension\IExtensionFactory']
        );

        $this->registerFactory(
            'extensionAdapter',
            $this->extensionAdapterFactoryClass,
            ['umi\templating\extension\adapter\IExtensionAdapterFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateEngineFactory()
    {
        return $this->getFactory('engine');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionFactory()
    {
        return $this->getFactory('extension');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAdapterFactory()
    {
        return $this->getFactory('extensionAdapter');
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IExtensionAdapterAware) {
            $object->setTemplatingExtensionAdapterFactory($this->getExtensionAdapterFactory());
        }

        if ($object instanceof IExtensionFactoryAware) {
            $object->setTemplatingExtensionFactory($this->getExtensionFactory());
        }

        if ($object instanceof ITemplateEngineAware) {
            $object->setTemplateEngineFactory($this->getTemplateEngineFactory());
        }
    }
}
 
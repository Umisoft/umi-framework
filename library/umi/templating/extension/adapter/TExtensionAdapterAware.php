<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\adapter;

use umi\templating\exception\RequiredDependencyException;

/**
 * Trait TExtensionAdapterAware
 */
trait TExtensionAdapterAware
{
    /**
     * @var IExtensionAdapterFactory $_templatingExtensionAdapterFactory фабрика
     */
    private $_templatingExtensionAdapterFactory;

    /**
     * Устанавливает фабрику для создания расширений шаблонизатора.
     * @param IExtensionAdapterFactory $factory фабрика
     */
    public function setTemplatingExtensionAdapterFactory(IExtensionAdapterFactory $factory)
    {
        $this->_templatingExtensionAdapterFactory = $factory;
    }

    /**
     * Возвращает адаптер расширения для шаблонизаторов.
     * @return IExtensionAdapter
     */
    protected final function createTemplatingExtensionAdapter()
    {
        return $this->getTemplatingExtensionAdapterFactory()
            ->createExtensionAdapter();
    }

    /**
     * Возвращает фабрику для создания адаптеров для расширения шаблонизаторов.
     * @return IExtensionAdapterFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getTemplatingExtensionAdapterFactory()
    {
        if (!$this->_templatingExtensionAdapterFactory) {
            throw new RequiredDependencyException(sprintf(
                'Templating extension adapter factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_templatingExtensionAdapterFactory;
    }
}
 
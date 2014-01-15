<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension;

use umi\templating\exception\RequiredDependencyException;
use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * Трейт для внедрения возможности создания коллекций помощников для шаблонов.
 */
trait TExtensionFactoryAware
{
    /**
     * @var IExtensionFactory $_templatingHelperCollectionFactory фабрика
     */
    private $_templatingExtensionFactory;

    /**
     * Устанавливает фабрику для создания расширений шаблонизатора.
     * @param IExtensionFactory $factory фабрика
     */
    public final function setTemplatingExtensionFactory(IExtensionFactory $factory)
    {
        $this->_templatingExtensionFactory = $factory;
    }

    /**
     * Создает коллекцию помощников для шаблонов.
     * @return IHelperCollection
     */
    protected final function createTemplateHelperCollection()
    {
        return $this->getTemplatingExtensionFactory()
            ->createHelperCollection();
    }

    protected final function getDefaultTemplateHelperCollection()
    {
        return $this->getTemplatingExtensionFactory()
            ->getDefaultHelperCollection();
    }

    /**
     * Возвращает фабрику для коллекций помощников для шаблонов.
     * @return IExtensionFactory
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getTemplatingExtensionFactory()
    {
        if (!$this->_templatingExtensionFactory) {
            throw new RequiredDependencyException(sprintf(
                'Templating extension factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_templatingExtensionFactory;
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\extension;

use umi\hmvc\exception\RequiredDependencyException;
use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * Trait TViewExtensionFactoryAware
 */
trait TViewExtensionFactoryAware
{
    /**
     * @var IViewExtensionFactory $_viewExtensionFactory
     */
    private $_viewExtensionFactory;

    public function setViewExtensionFactory(IViewExtensionFactory $factory)
    {
        $this->_viewExtensionFactory = $factory;
    }

    /**
     * @return IHelperCollection
     */
    protected final function createViewHelperCollection()
    {
        return $this->getViewExtensionFactory()
            ->createViewHelperCollection();
    }

    /**
     * @return IHelperCollection
     */
    protected final function getDefaultViewHelperCollection()
    {
        return $this->getViewExtensionFactory()
            ->getDefaultViewHelperCollection();
    }

    /**
     * @return IHelperCollection
     */
    protected final function createTemplateHelperCollection()
    {
        return $this->getViewExtensionFactory()
            ->createHelperCollection();
    }

    /**
     * @return IHelperCollection
     */
    protected final function getDefaultTemplateHelperCollection()
    {
        return $this->getViewExtensionFactory()
            ->getDefaultHelperCollection();
    }

    /**
     * todo: fix it?
     */
    abstract protected function injectContextToViewExtensionFactory(IViewExtensionFactory $factory);

    private final function getViewExtensionFactory()
    {
        if (!$this->_viewExtensionFactory) {
            throw new RequiredDependencyException(sprintf(
                'View extension factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        $this->injectContextToViewExtensionFactory($this->_viewExtensionFactory);

        return $this->_viewExtensionFactory;
    }

}
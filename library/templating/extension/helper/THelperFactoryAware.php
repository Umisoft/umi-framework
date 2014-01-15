<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper;

use umi\templating\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки создания помощников для шаблонов.
 */
trait THelperFactoryAware
{
    /**
     * @var IHelperFactory $_templatingHelperFactory фабрика помощников для шаблонов
     */
    private $_templatingHelperFactory;

    /**
     * Устанавливает фабрику помощников для шаблонов.
     * @param IHelperFactory $factory фабрика
     */
    public function setTemplatingHelperFactory(IHelperFactory $factory)
    {
        $this->_templatingHelperFactory = $factory;
    }

    /**
     * Создает помощник для шаблонов.
     * @param string $class класс помощника вида
     * @throws RequiredDependencyException если фабрика не была внедрена
     * @return callable
     */
    protected final function createTemplatingHelper($class)
    {
        if (!$this->_templatingHelperFactory) {
            throw new RequiredDependencyException(sprintf(
                'Templating helper factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_templatingHelperFactory
            ->createHelper($class);
    }
}
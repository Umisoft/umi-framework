<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine;

use umi\templating\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки работы с шаблонизаторами.
 */
trait TTemplateEngineAware
{
    /**
     * @var ITemplateEngineFactory $_templatingFactory фабрика
     */
    private $_templatingFactory;

    /**
     * Устанавливает фабрику для создания шаблонизаторов.
     * @param ITemplateEngineFactory $factory фабрика
     */
    public final function setTemplateEngineFactory(ITemplateEngineFactory $factory)
    {
        $this->_templatingFactory = $factory;
    }

    /**
     * Создает шаблонизатор заданного типа.
     * @param string $type тип шаблонизатора
     * @param array $options опции шаблонизатора
     * @return ITemplateEngine
     */
    protected final function createTemplateEngine($type, array $options = [])
    {
        return $this->getTemplateEngineFactory()
            ->createTemplateEngine($type, $options);
    }

    /**
     * Возвращает фабрику для создания шаблонизаторов.
     * @return ITemplateEngineFactory фабрика
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getTemplateEngineFactory()
    {
        if (!$this->_templatingFactory) {
            throw new RequiredDependencyException(sprintf(
                'Template engine factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_templatingFactory;
    }
}
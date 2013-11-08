<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

use umi\form\exception\RequiredDependencyException;

/**
 * Трейт для поддержки работы с формами.
 */
trait TFormAware
{
    /**
     * @var IEntityFactory $_formTools инструменты работы с формами.
     */
    private $_formEntityFactory;

    /**
     * Устанавливает инструменты для работы с формами
     * @param IEntityFactory $formEntityFactory
     */
    public final function setFormEntityFactory(IEntityFactory $formEntityFactory)
    {
        $this->_formEntityFactory = $formEntityFactory;
    }

    /**
     * Создает форму на основе конфига
     * @param array $config конфигурация
     * @throws RequiredDependencyException если инструменты для работы с формами не установлены
     * @return IForm
     */
    protected final function createForm(array $config)
    {
        if (!$this->_formEntityFactory) {
            throw new RequiredDependencyException(sprintf(
                'Form entity factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_formEntityFactory->createForm($config);
    }
}

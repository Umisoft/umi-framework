<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\response;

use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для внедрения возможности создания результата работы компонента.
 */
trait TComponentResponseAware
{
    /**
     * @var IComponentResponseFactory $_hmvcComponentResponseFactory фабрика
     */
    private $_hmvcComponentResponseFactory;

    /**
     * Устанавливает фабрику для создания результатов работы компонента.
     * @param IComponentResponseFactory $factory фабрика
     */
    public final function setComponentResponseFactory(IComponentResponseFactory $factory)
    {
        $this->_hmvcComponentResponseFactory = $factory;
    }

    /**
     * Создает результат работы компонента.
     * @return IComponentResponse
     */
    protected final function createComponentResponse()
    {
        return $this->getHMVCComponentResponseFactory()
            ->createComponentResponse();
    }

    /**
     * Возвращает фабрику результатов работы компонента.
     * @return IComponentResponseFactory фабрика
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getHMVCComponentResponseFactory()
    {
        if (!$this->_hmvcComponentResponseFactory) {
            throw new RequiredDependencyException(sprintf(
                'HMVC component response factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_hmvcComponentResponseFactory;
    }
}
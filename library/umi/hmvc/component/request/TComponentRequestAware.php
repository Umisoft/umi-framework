<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\request;

use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для внедрения возможности создания HTTP запроса компонента.
 */
trait TComponentRequestAware
{
    /**
     * @var IComponentRequestFactory $_hmvcComponentRequestFactory фабрика
     */
    private $_hmvcComponentRequestFactory;

    /**
     * Устанавливает фабрику для создания HTTP запроса компонента.
     * @param IComponentRequestFactory $factory фабрика
     */
    public final function setComponentRequestFactory(IComponentRequestFactory $factory)
    {
        $this->_hmvcComponentRequestFactory = $factory;
    }

    /**
     * Создает HTTP запрос для компонента.
     * @param array $uri URI запроса
     * @return IComponentRequest
     */
    protected final function createComponentRequest($uri)
    {
        return $this->getHVCComponentRequestFactory()
            ->createComponentRequest($uri);
    }

    /**
     * Возвращает фабрику результатов работы компонента.
     * @return IComponentRequestFactory фабрика
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getHVCComponentRequestFactory()
    {
        if (!$this->_hmvcComponentRequestFactory) {
            throw new RequiredDependencyException(sprintf(
                'HMVC component request factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_hmvcComponentRequestFactory;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\response\header;

use umi\http\exception\RequiredDependencyException;

/**
 * Трейт для внедрения возможности создания коллекции заголовков HTTP ответа.
 */
trait THeaderCollectionAware
{
    /**
     * @var IHeaderCollectionFactory $_httpHeaderCollectionFactory фабрика
     */
    private $_httpResponseHeaderCollectionFactory;

    /**
     * Устанавливает фабрику для создания коллекции заголовков HTTP ответа.
     * @param IHeaderCollectionFactory $factory
     */
    public final function setHttpHeaderCollectionFactory(IHeaderCollectionFactory $factory)
    {
        $this->_httpResponseHeaderCollectionFactory = $factory;
    }

    /**
     * Создает коллекцию заголовков для HTTP ответа.
     * @return IHeaderCollection
     */
    protected final function createHttpHeaderCollection()
    {
        return $this->getHttpResponseHeaderCollectionFactory()
            ->createHeaderCollection();
    }

    /**
     * Возвращает фабрику для создания коллекции заголовков HTTP ответа.
     * @return IHeaderCollectionFactory
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getHttpResponseHeaderCollectionFactory()
    {
        if (!$this->_httpResponseHeaderCollectionFactory) {
            throw new RequiredDependencyException(sprintf(
                'HTTP response header collection factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_httpResponseHeaderCollectionFactory;
    }
}
 
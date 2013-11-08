<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request\param;

use umi\http\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки создания коллекции параметров.
 * @internal
 */
trait TParamCollectionAware
{
    /**
     * @var IParamCollectionFactory $_paramCollectionFactory
     */
    private $_httpParamCollectionFactory;

    /**
     * Устанавливает фабрику коллекции параметров.
     * @param IParamCollectionFactory $paramCollectionFactory
     * @return mixed
     */
    public final function setParamCollectionFactory(IParamCollectionFactory $paramCollectionFactory)
    {
        $this->_httpParamCollectionFactory = $paramCollectionFactory;
    }

    /**
     * Создает коллекцию параметров.
     * @param array $params ссылка на массив параметров
     * @return self
     */
    protected final function createParamCollection(array &$params)
    {
        return $this->getParamCollectionFactory()
            ->createParamCollection($params);
    }

    /**
     * Возвращает фабрику для создания коллекции параметров запроса.
     * @return IParamCollectionFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private function getParamCollectionFactory()
    {
        if (!$this->_httpParamCollectionFactory) {
            throw new RequiredDependencyException(sprintf(
                'Param collection factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_httpParamCollectionFactory;
    }
}
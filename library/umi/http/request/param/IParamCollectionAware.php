<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request\param;

/**
 * Интерфейс для внедрения поддержки создания коллекции параметров.
 * @internal
 */
interface IParamCollectionAware
{
    /**
     * Устанавливает фабрику коллекции параметров.
     * @param IParamCollectionFactory $paramCollectionFactory
     * @return mixed
     */
    public function setParamCollectionFactory(IParamCollectionFactory $paramCollectionFactory);
}
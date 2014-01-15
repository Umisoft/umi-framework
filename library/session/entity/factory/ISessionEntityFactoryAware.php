<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\factory;

/**
 * Интерфейс для внедрения возможности создания пространств имен в сессии.
 */
interface ISessionEntityFactoryAware
{
    /**
     * Устанавливает фабрику для создания простанств имен.
     * @param ISessionEntityFactory $nsFactory фабрика
     */
    public function setNamespaceFactory(ISessionEntityFactory $nsFactory);
}

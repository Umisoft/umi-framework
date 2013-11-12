<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox;

use umi\orm\collection\ICollectionManager;
use umi\orm\manager\IObjectManager;
use umi\orm\metadata\IMetadataManager;
use umi\orm\persister\IObjectPersister;
use umi\toolkit\toolbox\IToolbox;

/**
 * Инструментарий ORM.
 */
interface IORMTools extends IToolbox
{
    /**
     * Возвращает менеджер объектов
     * @return IObjectManager
     */
    public function getObjectManager();

    /**
     * Возвращает менеджер метаданных
     * @return IMetadataManager
     */
    public function getMetadataManager();

    /**
     * Возвращает менеджер метаданных
     * @return ICollectionManager
     */
    public function getCollectionManager();

    /**
     * Возвращает синхронизатор объектов
     * @return IObjectPersister
     */
    public function getObjectPersister();

}

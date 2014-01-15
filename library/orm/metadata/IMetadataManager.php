<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\orm\exception\IException;

/**
 * Менеджер метаданных.
 */
interface IMetadataManager
{

    /**
     * Возвращает метаданные для коллекции
     * @param string $collectionName имя коллекции
     * @throws IException если не удалось получить метаданные
     * @return IMetadata
     */
    public function getMetadata($collectionName);
}

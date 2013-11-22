<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\metadata\IMetadata;

/**
 * Фабрика коллекий объектов.
 */
interface ICollectionFactory
{

    /**
     * Типы коллекций
     */
    const TYPE_SIMPLE = 'simple';
    const TYPE_SIMPLE_HIERARCHIC = 'hierarchic';
    const TYPE_LINKED_HIERARCHIC = 'linked';
    const TYPE_COMMON_HIERARCHY = 'hierarchy';

    /**
     * Создает экземпляр простой коллекции объектов.
     * @param string $collectionName имя коллекции.
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции.
     * @return ICollection
     */
    public function create($collectionName, IMetadata $metadata, array $config);
}

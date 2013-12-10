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
 * Фабрика коллекций объектов.
 */
interface ICollectionFactory
{

    /**
     * Тип простой коллекции объектов
     */
    const TYPE_SIMPLE = 'simple';
    /**
     * Тип иерархической коллекции объектов
     */
    const TYPE_SIMPLE_HIERARCHIC = 'hierarchic';
    /**
     * Тип связанной иерархической коллекции объектов
     */
    const TYPE_LINKED_HIERARCHIC = 'linked';
    /**
     * Тип общей иерархической коллекции
     */
    const TYPE_COMMON_HIERARCHY = 'hierarchy';

    /**
     * Создает экземпляр коллекции объектов.
     * @param string $collectionName имя коллекции.
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции.
     * @return ICollection
     */
    public function create($collectionName, IMetadata $metadata, array $config);

}

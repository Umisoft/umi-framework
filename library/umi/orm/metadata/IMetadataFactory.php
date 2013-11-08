<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use Traversable;
use umi\orm\metadata\field\IField;

/**
 * Фабрика для создания метаданных.
 */
interface IMetadataFactory
{

    /**
     * Создает metadata.
     * @param string $collectionName имя коллекции объектов, которую описывает metadata
     * @param array|Traversable $config конфигурация метаданных
     * @return IMetadata
     */
    public function create($collectionName, $config);

    /**
     * Создает прототип источника данных коллекции.
     * @param array $config конфигурация
     * @return ICollectionDataSource
     */
    public function createDataSource(array $config);

    /**
     * Создает тип объекта.
     * @param string $typeName имя типа
     * @param array $config конфигурация
     * @param IMetadata $metadata метаданные, к которым относится тип
     * @return IObjectType
     */
    public function createObjectType($typeName, array $config, IMetadata $metadata);

    /**
     * Создает поле.
     * @param string $fieldName имя поля
     * @param array $config конфигурация
     * @return IField
     */
    public function createField($fieldName, array $config);

}

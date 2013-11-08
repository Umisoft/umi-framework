<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\orm\exception\NonexistentEntityException;
use umi\orm\metadata\field\IField;

/**
 * Метаданные коллекции объектов (типы и поля).
 * Иерархическая взаимосвязь типов должна быть отражена в имени типа (materialized path).<br />
 * Например: базовый тип - banner, дочерний тип banner.flash и т.д.<br />
 * Это более оптимально для загрузки типа, поиска объектов, а также лучше воспринимается человеком.
 */
interface IMetadata
{

    /**
     * Возвращает имя коллекции объектов, которую описывает metadata
     * @return string
     */
    public function getCollectionName();

    /**
     * Возвращает источник данных коллекции
     * @return ICollectionDataSource
     */
    public function getCollectionDataSource();

    /**
     * Возвращает список имен всех типов коллекции
     * @return array
     */
    public function getTypesList();

    /**
     * Возвращает базовый тип коллекции
     * @throws NonexistentEntityException если базовый тип не существует
     * @return IObjectType
     */
    public function getBaseType();

    /**
     * Проверяет существует ли тип с указанным именем
     * @param string $typeName имя типа
     * @return bool
     */
    public function getTypeExists($typeName);

    /**
     * Возвращает экземпляр типа по имени
     * @param string $typeName имя типа
     * @throws NonexistentEntityException если тип не существует
     * @return IObjectType
     */
    public function getType($typeName);

    /**
     * Возвращает непосредственного родителя типа либо null, если тип базовый
     * @param $typeName имя типа
     * @throws NonexistentEntityException если тип с указанным именем не существует или не существует родителя
     * @return null|IObjectType
     */
    public function getParentType($typeName);

    /**
     * Возвращает список имен непосредственных дочерних типов
     * @param string $typeName имя типа, по умолчанию имя базового типа
     * @throws NonexistentEntityException если родительский тип с указанным именем не существует
     * @return array список из имен типов
     */
    public function getChildTypesList($typeName = IObjectType::BASE);

    /**
     * Возвращает список имен дочерних типов на указанную глубину
     * @param string $typeName имя типа, по умолчанию имя базового типа
     * @param integer $depth глубина, по умолчанию выборка на всю глубину
     * @throws NonexistentEntityException если родительский тип с указанным именем не существует
     * @return array список из имен типов
     */
    public function getDescendantTypesList($typeName = IObjectType::BASE, $depth = null);

    /**
     * Возвращает список имен всех полей коллекции
     * @return array список из имен полей
     */
    public function getFieldsList();

    /**
     * Возвращает список полей коллекции
     * @return array в формате [fieldName => IField, ...]
     */
    public function getFields();

    /**
     * Проверяет существует ли поле с указанным именем
     * @param string $fieldName имя поля
     * @return bool
     */
    public function getFieldExists($fieldName);

    /**
     * Возвращает поле с указанным именем
     * @param $fieldName имя поля
     * @throws NonexistentEntityException если поля с указанным именем уже существует
     * @return IField
     */
    public function getField($fieldName);

    /**
     * Находит типы, содержащие указанные поля, начиная с заданного типа
     * @param array $fieldNames массив имен полей
     * @param string $typeName имя типа, по умолчанию имя базового типа
     * @return array список имен типов
     */
    public function getTypesByFields(array $fieldNames, $typeName = IObjectType::BASE);

    /**
     * Находит связанное по target-коллекции поле
     * @internal
     * @param string $targetFieldName имя связанного поля
     * @param string $targetCollectionName имя target-коллекции
     * @throws NonexistentEntityException если не удалось найти поле
     * @return IField
     */
    public function getFieldByTarget($targetFieldName, $targetCollectionName);

    /**
     * Находит связанное по bridge-коллекции поле
     * @internal
     * @param string $relatedFieldName имя связанного поля
     * @param string $bridgeCollectionName имя bridge-коллекции, в которой находится связанное поле
     * @throws NonexistentEntityException если не удалось найти поле
     * @return IField
     */
    public function getFieldByRelation($relatedFieldName, $bridgeCollectionName);

}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\IException;
use umi\orm\exception\LoadEntityException;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\metadata\field\special\GuidField;
use umi\orm\metadata\field\special\IdentifyField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use umi\orm\selector\ISelector;

/**
 * Простая коллекция ORM-объектов.
 */
interface ICollection
{

    /**
     * Возвращает имя коллекции
     * @return string
     */
    public function getName();

    /**
     * Возвращает метаданные коллекции
     * @return IMetadata
     */
    public function getMetadata();

    /**
     * Возвращает объект по уникальному GUID.
     * @param integer|string $guid GUID объекта
     * @param bool $withLocalization загружать ли значения локализованных свойств объекта.
     * По умолчанию выключено.
     * @throws IException если не удалось получить объект
     * @return IObject|IHierarchicObject
     */
    public function get($guid, $withLocalization = false);

    /**
     * Возвращает объект по уникальному идентификатору в БД.
     * Используется ORM для внутренних целей, запрещено использовать в высокоуровневом коде.
     * @internal
     * @param integer|string $objectId
     * @param bool $withLocalization загружать ли значения локализованных свойств объекта.
     * По умолчанию выключено.
     * @throws IException если не удалось получить объект
     * @return IObject|IHierarchicObject
     */
    public function getById($objectId, $withLocalization = false);

    /**
     * Возвращает новый селектор для формирования выборки объектов коллекции.
     * @return ISelector
     */
    public function select();

    /**
     * Возвращает селектор с пустым рельтатом выборки.
     * @return ISelector
     */
    public function emptySelect();

    /**
     * Создает селектор для связи ManyToMany на данную коллекцию
     * @internal
     * @param IObject $object
     * @param ManyToManyRelationField $manyToManyRelationField
     * @return ISelector
     */
    public function getManyToManySelector(IObject $object, ManyToManyRelationField $manyToManyRelationField);

    /**
     * Проверяет, принадлежит ли объект данной коллекции
     * @param IObject $object
     * @return bool
     */
    public function contains(IObject $object);

    /**
     * Удаляет объект из коллекции.
     * @param IObject $object удаляемый объект
     * @throws NotAllowedOperationException при попытке удалить объект, который не принадлежит данной коллекции
     * @return self
     */
    public function delete(IObject $object);

    /**
     * Догружает все свойства объекта.
     * @param IObject $object
     * @param bool $withLocalization загружать ли все локализованные поля
     */
    public function fullyLoadObject(IObject $object, $withLocalization = false);

    /**
     * Возвращает поля, которые обязательны для загрузки объекта коллекции.
     * @internal
     * @return IField[]
     */
    public function getForcedFieldsToLoad();

    /**
     * Возвращает поле, которое используется у базового типа коллекции в качестве первичного ключа
     * @throws NonexistentEntityException если такого поля не существует
     * @return IdentifyField
     */
    public function getIdentifyField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции
     * для хранения уникального глобального идентификатора объекта
     * @throws NonexistentEntityException если такого поля не существует
     * @return GuidField
     */
    public function getGUIDField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о версии объекта
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getVersionField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о типе
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getObjectTypeField();

    /**
     * Возвращает alias для источника данных коллекции
     * @internal
     * @return string
     */
    public function getSourceAlias();

    /**
     * Возвращает alias для поля в запросе
     * @internal
     * @param string $fieldName
     * @return string
     */
    public function getFieldAlias($fieldName);

    /**
     * Загружает объект в коллекцию
     * @internal
     * @param IObjectType $objectType тип объекта
     * @param array $objectInfo информация об объекте
     * @throws LoadEntityException если не удалось загрузить объект
     * @return IObject
     */
    public function loadObject(IObjectType $objectType, array $objectInfo);

    /**
     * Запускает запросы на добавление в БД нового объекта коллекции.
     * @internal
     * @param IObject $object
     * @return mixed
     */
    public function persistNewObject(IObject $object);

    /**
     * Запускает запросы на изменение объекта коллекции.
     * @internal
     * @param IObject $object
     */
    public function persistModifiedObject(IObject $object);

    /**
     * Запускает запросы на удаление объекта коллекции.
     * @internal
     * @param IObject $object
     */
    public function persistDeletedObject(IObject $object);
}

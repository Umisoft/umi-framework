<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use umi\dbal\exception\NonexistentEntityException;

/**
 * Интерфейс схемы таблицы в БД.
 */
interface ITableScheme
{
    /**
     * Возвращает имя таблицы.
     * @return string
     */
    public function getName();

    /**
     * Проверяет, была ли изменена схема таблицы.
     * @return bool
     */
    public function getIsModified();

    /**
     * Проверяет, является ли схема новой.
     * @return bool
     */
    public function getIsNew();

    /**
     * Проверяет, является ли схема удаленной.
     * @return bool
     */
    public function getIsDeleted();

    /**
     * Устанавливает/снимает флаг "схема изменена".
     * @param bool $isModified
     * @return self
     */
    public function setIsModified($isModified = true);

    /**
     * Устанавливает/снимает флаг "новая схема".
     * @param bool $isNew
     * @return self
     */
    public function setIsNew($isNew = true);

    /**
     * Устанавливает/снимает флаг "удаленная схема".
     * @param bool $isDeleted
     * @return self
     */
    public function setIsDeleted($isDeleted = true);

    /**
     * Возвращает collation таблицы.
     * @return string
     */
    public function getCollation();

    /**
     * Возвращает charset таблицы.
     * @return string
     */
    public function getCharset();

    /**
     * Возвращает engine таблицы.
     * @return string
     */
    public function getEngine();

    /**
     * Возвращает комметарий к таблице.
     * @return string
     */
    public function getComment();

    /**
     * Устанавливает collation таблицы.
     * @param string $collation
     * @return self
     */
    public function setCollation($collation);

    /**
     * Устанавливает комментарий для таблицы.
     * @param string $comment
     * @return self
     */
    public function setComment($comment);

    /**
     * Устанавливает charset таблицы.
     * @param string $charset
     * @return self
     */
    public function setCharset($charset);

    /**
     * Устанавливает engine таблицы.
     * @param string $engine
     * @return self
     */
    public function setEngine($engine);

    /**
     * Добавляет новый столбец в таблицу или возвращает существующий,
     * при этом меняет тип столбца, если он отличается от текущего.
     * @param string $name имя столбца
     * @param string $type абстрактный тип столбца
     * @param array $options список параметров столбца
     * @return IColumnScheme
     */
    public function addColumn($name, $type, array $options = []);

    /**
     * Удаляет столбец.
     * @param string $name имя столбца
     * @throws NonexistentEntityException, если столбец не существует
     * @return self
     */
    public function deleteColumn($name);

    /**
     * Возвращает список новых столбцов.
     * @internal
     * @return IColumnScheme[]
     */
    public function getNewColumns();

    /**
     * Возвращает список измененных столбцов.
     * @internal
     * @return IColumnScheme[]
     */
    public function getModifiedColumns();

    /**
     * Возвращает список удаленных столбцов.
     * @internal
     * @return IColumnScheme[]
     */
    public function getDeletedColumns();

    /**
     * Загружает / обновляет схему из БД.
     * @return self
     */
    public function reload();

    /**
     * Возвращает массив индексов таблицы.
     * @return IIndexScheme[]
     */
    public function getIndexes();

    /**
     * Возвращает внешние ключи таблицы.
     * @return IConstraintScheme[]
     */
    public function getConstraints();

    /**
     * Возвращает первичный ключ таблицы.
     * @return IIndexScheme|null
     */
    public function getPrimaryKey();

    /**
     * Удаляет первичный ключ.
     * @throws NonexistentEntityException если первичного ключа нет
     * @return self
     */
    public function deletePrimaryKey();

    /**
     * Устанавливает первичный ключ для таблицы или получает текущий.
     * @param string $columnName имя столбца
     * [@param string $columnName,... ]
     * @return IIndexScheme
     */
    public function setPrimaryKey($columnName);

    /**
     * Возвращает индекс таблицы.
     * @param string $name имя индекса
     * @throws NonexistentEntityException если индекса с таким именем не существует
     * @return IIndexScheme
     */
    public function getIndex($name);

    /**
     * Добавляет индекс в таблицу или возвращает существующий.
     * @param string $name имя
     * @return IIndexScheme
     */
    public function addIndex($name);

    /**
     * Удаляет индекс.
     * @param string $name индекса
     * @throws NonexistentEntityException если индекса с таким именем не существует
     * @return self
     */
    public function deleteIndex($name);

    /**
     * Проверяет, существует ли индекс.
     * @param string $name имя внешнего ключа
     * @return bool
     */
    public function getIndexExists($name);

    /**
     * Возвращает список новых индексов.
     * @internal
     * @return IIndexScheme[]
     */
    public function getNewIndexes();

    /**
     * Возвращает список измененных индексов.
     * @internal
     * @return IIndexScheme[]
     */
    public function getModifiedIndexes();

    /**
     * Возвращает массив удаленных индексов.
     * @internal
     * @return IIndexScheme[]
     */
    public function getDeletedIndexes();

    /**
     * Возвращает внешний ключ таблицы.
     * @param string $name имя внешнего ключа
     * @throws NonexistentEntityException если внешнего ключа с таким именем не существует
     * @return IConstraintScheme
     */
    public function getConstraint($name);

    /**
     * Добавляет новый внешний ключ или получает существующий с таким именем.
     * При этом можно изменить параметры внешнего ключа.
     * @param string $name имя внешнего ключа
     * @param string $columnName имя столбца, на которого действует ограничение внешнего ключа
     * @param string $referenceTableName имя связанной таблицы, значения которой являются ограничениями внешнего ключа
     * @param string $referenceColumnName имя столбца связанной таблицы, значения которого являются ограничениями внешнего ключа
     * @param string $onDeleteAction действие при удалении строк из связанной таблицы
     * @param string $onUpdateAction действие при обновлении строк из связанной таблицы
     * @return IConstraintScheme
     */
    public function addConstraint(
        $name,
        $columnName,
        $referenceTableName,
        $referenceColumnName,
        $onDeleteAction = null,
        $onUpdateAction = null
    );

    /**
     * Удаляет внешний ключ.
     * @param string $name имя внешнего ключа
     * @throws NonexistentEntityException если ключа с таким именем не существует
     * @return self
     */
    public function deleteConstraint($name);

    /**
     * Проверяет существует ли внешний ключ.
     * @param string $name имя внешнего ключа
     * @return bool
     */
    public function getConstraintExists($name);

    /**
     * Возвращает список новых внешних ключей.
     * @internal
     * @return IConstraintScheme[]
     */
    public function getNewConstraints();

    /**
     * Возвращает список удаленных внешних ключей.
     * @internal
     * @return IConstraintScheme[]
     */
    public function getDeletedConstraints();

    /**
     * Возвращает список измененных внешних ключей.
     * @internal
     * @return IConstraintScheme[]
     */
    public function getModifiedConstraints();

    /**
     * Возвращает запрос для создания/изменения схемы таблицы.
     * @thrown DbDriverException если в процессе генерации запросов
     * произошла ошибка
     * @return array запросы для миграции
     */
    public function getMigrationQueries();

    /**
     * Возвращает схему столбца таблицы.
     * @param string $name имя столбца
     * @throws NonexistentEntityException, если столбец не существует
     * @return IColumnScheme
     */
    public function getColumn($name);

    /**
     * Проверяет существует ли столбец.
     * @param string $name имя столбца
     * @return bool
     */
    public function getColumnExists($name);

    /**
     * Возвращает список схем всех столбцов таблицы
     * @return IColumnScheme[] массив вида array(columnName => IColumnScheme, ...)
     */
    public function getColumns();
}

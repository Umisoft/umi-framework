<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

/**
 * Интерфейс схемы столбца таблицы БД.
 */
interface IColumnScheme
{

    const TYPE_INT = 'int';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_REAL = 'real';
    const TYPE_BOOL = 'bool';

    const TYPE_SERIAL = 'serial';
    const TYPE_RELATION = 'relation';

    const TYPE_VARCHAR = 'varchar';
    const TYPE_CHAR = 'char';

    const TYPE_TEXT = 'text';
    const TYPE_BLOB = 'blob';

    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_DATETIME = 'datetime';

    const SIZE_NUMERIC_BIG = 8;
    const SIZE_NUMERIC_NORMAL = 4;
    const SIZE_NUMERIC_MEDIUM = 3;
    const SIZE_NUMERIC_SMALL = 2;
    const SIZE_NUMERIC_TINY = 1;

    const SIZE_TEXT_LONG = 4294967300;
    const SIZE_TEXT_MEDIUM = 16777218;
    const SIZE_TEXT_NORMAL = 65537;
    const SIZE_TEXT_TINY = 256;

    const OPTION_TYPE = 'type';
    const OPTION_SIZE = 'size';
    const OPTION_LENGTH = 'length';
    const OPTION_DECIMALS = 'decimals';
    const OPTION_UNSIGNED = 'unsigned';
    const OPTION_ZEROFILL = 'zerofill';
    const OPTION_NULLABLE = 'nullable';
    const OPTION_AUTOINCREMENT = 'autoincrement';
    const OPTION_PRIMARY_KEY = 'pk';
    const OPTION_COMMENT = 'comment';
    const OPTION_COLLATION = 'collation';
    const OPTION_DEFAULT_VALUE = 'default';

    /**
     * Возвращает имя столбца
     * @return string
     */
    public function getName();

    /**
     * Устанавливает тип столбца, сбрасывая все установленные опции.
     * @param string $type тип столбца
     * @param array $options список параметров столбца
     * @internal
     * @return self
     */
    public function setType($type, array $options = []);

    /**
     * Устанавливает свойство столбца
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setOption($name, $value);

    /**
     * Может ли значение столбца быть NULL
     * @return bool
     */
    public function getIsNullable();

    /**
     * Возвращает способ сравнения столбца
     * @return string
     */
    public function getCollation();

    /**
     * Возвращает внутренний тип данных столбца, в синтаксисе конкретного драйвера БД,
     * используется для построения запросов к БД
     * @return string
     */
    public function getInternalType();

    /**
     * Возвращает длину столбца
     * @return int
     */
    public function getLength();

    /**
     * Возвращает количество цифр после запятой для столбцов с плавющей точкой
     * @return int
     */
    public function getDecimals();

    /**
     * Возвращает для числовых столбцов признак хранения числа без знака
     * @return bool
     */
    public function getIsUnsigned();

    /**
     * Возвращает для числовых стобцов признак заполнения нулями
     * @return bool
     */
    public function getIsZerofill();

    /**
     * Возвращает значение столбца "по умолчанию"
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Возвращает комментарий к колонке
     * @return string
     */
    public function getComment();

    /**
     * Проверяет, является ли столбец Primary Key
     * @return bool
     */
    public function getIsPk();

    /**
     * Проверяет, является ли столбец Auto Increment
     * @return bool
     */
    public function getIsAutoIncrement();

    /**
     * Проверяет, была ли изменена схема
     * @return bool
     */
    public function getIsModified();

    /**
     * Установливает/снимает флаг "схема изменена"
     * @internal
     * @param bool $isModified
     * @return self
     */
    public function setIsModified($isModified = true);

    /**
     * Установливает/снимает флаг "схема удалена"
     * @internal
     * @param bool $isDeleted
     * @return self
     */
    public function setIsDeleted($isDeleted = true);

    /**
     * Проверяет, является ли схема удаленной
     * @return bool
     */
    public function getIsDeleted();

    /**
     * Проверяет, является ли схема новой
     * @return bool
     */
    public function getIsNew();

    /**
     * Устанавливает/снимает флаг "новая схема"
     * @internal
     * @param bool $isNew
     * @return self
     */
    public function setIsNew($isNew = true);

}

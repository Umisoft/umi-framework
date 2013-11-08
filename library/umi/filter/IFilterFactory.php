<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter;

use umi\filter\exception\RuntimeException;

/**
 * Интерфейс фабрики создания валидаторов.
 */
interface IFilterFactory
{
    /**
     * Фильтр Boolean типа
     * Преобразует значение в true или false используя стандартное приведение типов php
     * Опции:
     *    [optional_values] => [[key] => [value]] доп. значения, которые требуется привести к boolean (например 'yes' => true)
     */
    const TYPE_BOOLEAN = "boolean";
    /**
     * Преобразует все возможные символы в соответствующие HTML-сущности
     * Опции:
     *    [flags] => [] флаги функции htmlentities {@see http://php.net/manual/en/function.htmlentities.php}
     *    [encoding] => [] кодировка для функции htmlentities {@see http://php.net/manual/en/function.htmlentities.php}
     */
    const TYPE_HTML_ENTITIES = "htmlEntities";
    /**
     * Преобразует значение к типу integer.
     * Опции:
     *    [base] => [] система счисления {@see http://www.php.net/manual/en/function.intval.php}
     */
    const TYPE_INT = "int";
    /**
     * Преобразует значение к NULL
     * Опции:
     *    [optional_values] => [[value]] доп. значение для преоброзования
     */
    const TYPE_NULL = "null";
    /**
     * Фильтр по регулярному выражению.
     * Опции:
     *    [pattern*] => [регулярное выражение]
     *    [replacement*] => [значение]
     *    [limit] => [ограничение количества замен]
     */
    const TYPE_REGEXP = "regexp";
    /**
     * Преобразует все символы строки к нижнему регистру
     * Опции:
     *    [encoding] => кодировка
     */
    const TYPE_STRING_TO_LOWER = "stringToLower";
    /**
     * Преобразует все символы строки к верхнему регистру
     * Опции:
     *    [encoding] => кодировка
     */
    const TYPE_STRING_TO_UPPER = "stringToUpper";
    /**
     * Удаляет пробелы (или другие символы) из начала и конца строки
     * Опции:
     *    [charlist] => Cписок символов для удаления
     */
    const TYPE_STRING_TRIM = "stringTrim";
    /**
     * Преобразует символы переноса строки в пробелы
     */
    const TYPE_STRIP_NEW_LINES = "stripNewLines";
    /**
     * Удаляет HTML тэги из строки
     * Опции:
     *    [allowed] => [[value]] тэги, которые не нужно удалять
     */
    const TYPE_STRIP_TAGS = "stripTags";

    /**
     * Создает фильтр определенного типа. Устанавливет ему опции (если переданы).
     * @param string $type тип фильтра
     * @param array $options опции фильтра
     * @throws RuntimeException если тип фильтра не найден
     * @return IFilter созданный фильтр
     */
    public function createFilter($type, array $options = []);

    /**
     * Создает коллекцию фильтров на основе массива.
     * @example ['null' => []]
     * @param array $config конфигурация фильтров
     * @return IFilterCollection
     */
    public function createFilterCollection(array $config);
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use PDOStatement;
use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\IException;

/**
 * Интерфейс построителя всех типов запросов.
 */
interface IQueryBuilder
{
    /**
     * Прямой порядок сортировки
     */
    const ORDER_ASC = 'ASC';
    /**
     * Обратный порядок сортировки
     */
    const ORDER_DESC = 'DESC';

    /**
     * Генерирует и возвращает шаблон запроса.
     * @param IDbDriver $driver используемый драйвер БД
     * @return string sql
     */
    public function getSql(IDbDriver $driver = null);

    /**
     * Возвращает драйвер БД, используемый для запроса
     * @return IDbDriver
     */
    public function getDbDriver();

    /**
     * Проверяет, был ли выполнен запрос
     * @return bool
     */
    public function getExecuted();

    /**
     * Связывает плейсхолдер с значением
     * @internal
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $value значение плейсхолдера
     * @param string $phpType тип плейсхолдера ('string', 'integer', 'boolean', 'array', ...)
     * http://ru2.php.net/manual/en/function.gettype.php
     * @return self
     */
    public function bindValue($placeholder, $value, $phpType);

    /**
     * Устанавливает строковое значение плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param string $value
     * @return self
     */
    public function bindString($placeholder, $value);

    /**
     * Устанавливает целочисленное значение плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param integer $value
     * @return self
     */
    public function bindInt($placeholder, $value);

    /**
     * Устанавливает булевое значение плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param boolean $value
     * @return self
     */
    public function bindBool($placeholder, $value);

    /**
     * Устанавливает бинарный массив данных в качестве значения плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param string $value
     * @return self
     */
    public function bindBlob($placeholder, $value);

    /**
     * Устанавливает вещественное значение плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param float $value
     * @return self
     */
    public function bindFloat($placeholder, $value);

    /**
     * Устанавливает NULL в качестве значения плейсхолдера
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @return self
     */
    public function bindNull($placeholder);

    /**
     * Устанавливает значение плейсхолдера, представляющее собой список. <br />
     * Используйте этот метод только для IN, NOT IN условий.<br />
     * Подготовленный запрос будет иметь вид: IN (:$placeholder0, :$placeholder1, :$placeholder2) значения
     * плейсхолдеров будет автоматически биндится
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param array $value
     * @return self
     */
    public function bindArray($placeholder, array $value);

    /**
     * Устанавливает значение плейсхолдера, представляющее собой sql-выражение. <br />
     * Внимание! Геренатор запросов подставляет это выражение в запрос "как есть".<br />
     * Cледить за sql-injection's, а так же за поддержкой этих выражений
     * альтернативными драйверами - задача использующего компонента
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param string $value выражение
     * @return self
     */
    public function bindExpression($placeholder, $value);

    /**
     * Возвращает значения, связанные с плейсхолдерами
     * @internal
     * @return array вида array(':placeholder' => value, ...)
     */
    public function getPlaceholderValues();

    /**
     * Связывает плейсхолдер с PHP-переменной типа string.
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * @return self
     */
    public function bindVarString($placeholder, &$variable);

    /**
     * Связывает плейсхолдер с PHP-переменной типа integer.
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * @return self
     */
    public function bindVarInt($placeholder, &$variable);

    /**
     * Связывает плейсхолдер с PHP-переменной типа boolean.
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * @return self
     */
    public function bindVarBool($placeholder, &$variable);

    /**
     * Связывает плейсхолдер с PHP-переменной типа float.
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * @return self
     */
    public function bindVarFloat($placeholder, &$variable);

    /**
     * Связывает результат выборки по указанному столбцу с PHP-переменной.
     * Результат будет преобразован к строковому типу.
     * @param string $columnName имя столбца
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * Если переменная не указана, то значение столбца в любом случае будет
     * приведено к соответсвующему типу.
     * @return self
     */
    public function bindColumnString($columnName, &$variable = null);

    /**
     * Связывает результат выборки по указанному столбцу с PHP-переменной.
     * Результат будет преобразован к целому числу.
     * @param string $columnName имя столбца
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * Если переменная не указана, то значение столбца в любом случае будет
     * приведено к соответсвующему типу.
     * @return self
     */
    public function bindColumnInt($columnName, &$variable = null);

    /**
     * Связывает результат выборки по указанному столбцу с PHP-переменной.
     * Результат будет преобразован к целому числу.
     * @param string $columnName имя столбца
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * Если переменная не указана, то значение столбца в любом случае будет
     * приведено к соответсвующему типу.
     * @return self
     */
    public function bindColumnBool($columnName, &$variable = null);

    /**
     * Связывает результат выборки по указанному столбцу с PHP-переменной.
     * Результат будет преобразован к целому числу.
     * @param string $columnName имя столбца
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * Если переменная не указана, то значение столбца в любом случае будет
     * приведено к соответсвующему типу.
     * @return self
     */
    public function bindColumnFloat($columnName, &$variable = null);

    /**
     * Запускает запрос и возвращает результат
     * @throws IException
     * @return IQueryResult
     */
    public function execute();

    /**
     * Возвращает PDOStatement
     * @internal
     * @return PDOStatement
     */
    public function getPDOStatement();
}

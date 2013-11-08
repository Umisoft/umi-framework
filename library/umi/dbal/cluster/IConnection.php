<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster;

use PDOStatement;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\RuntimeException;

/**
 * Интерфейс соединения с БД.
 * Оперделяет единый интерфейс доступа к данным.
 */
interface IConnection
{

    /**
     * Тип события, которое происходит перед подготовкой запроса.
     * @param string $sql шаблон запроса
     * @param IQueryBuilder|null $queryBuilder билдер запроса
     */
    const EVENT_BEFORE_PREPARE_QUERY = 'umi:db:eventBeforePrepareQuery';
    /**
     * Тип события, которое происходит после выполнения запроса.
     * @param PDOStatement $preparedStatement подготовленный запрос
     * @param IQueryBuilder|null $queryBuilder билдер запроса
     */
    const EVENT_AFTER_EXECUTE_QUERY = 'umi:db:eventAfterExecuteQuery';

    /**
     * Подготавливает запрос на выборку данных,
     * определяет список столбцов для выборки. <br />
     * Список столбцов передается в параметрах метода.<br />
     * Если столбцы не переданы, будет сформирован запрос, содержащий все столбцы (SELECT *)<br />
     * [@param string $columnName список имен столбцов]
     * @return ISelectBuilder
     */
    public function select();

    /**
     * Подготавливает запрос на вставку данных
     * @param string $tableName имя таблицы
     * @param bool $isIgnore игнорировать ошибки и duplicate-key конфликты
     * @return IInsertBuilder
     */
    public function insert($tableName, $isIgnore = false);

    /**
     * Подготавливает запрос на обновление данных
     * @param string $tableName имя таблицы для
     * @param bool $isIgnore игнорировать ошибки и duplicate-key конфликты
     * @return IUpdateBuilder
     */
    public function update($tableName, $isIgnore = false);

    /**
     * Подготавливает запрос на удаление данных
     * @param string $tableName имя таблицы
     * @return IDeleteBuilder
     */
    public function delete($tableName);

    /**
     * Выполняет прямой запрос на выборку данных.
     * Можно использовать только для тестов, для реализации драйверов БД,
     * для быстрых операций с БД не в "коробочных" целях
     * @internal
     * @param string $sql sql-запрос
     * @param array $params массив параметров для подготовленных запросов
     * @throws RuntimeException если в процессе выполнения запроса произошли ошибки
     * @return PDOStatement
     */
    public function selectInternal($sql, array $params = null);

    /**
     * Выполняет прямой запрос на модификацию данных
     * @internal
     * @param string $sql sql-запрос
     * @param array $params массив параметров для подготовленных запросов
     * @throws RuntimeException если в процессе выполнения запроса произошли ошибки
     * @return int количество затронутых запросом строк
     */
    public function modifyInternal($sql, array $params = null);

    /**
     * Возвращает экземпляр используемого драйвера БД
     * @return IDbDriver
     */
    public function getDbDriver();
}

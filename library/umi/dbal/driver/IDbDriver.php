<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use PDO;
use PDOStatement;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\exception\IException;
use umi\dbal\exception\NonexistentEntityException;
use umi\dbal\exception\RuntimeException;
use umi\event\IEventObservant;

/**
 * Интерфейс драйвера БД.
 */
interface IDbDriver extends IEventObservant
{
    /**
     * Проверяет, поддерживается ли используемый PDO-драйвер
     * @return bool
     */
    public function isAvailable();

    /**
     * Возвращает схему таблицы
     * @param string $name имя таблицы
     * @throws IException если не удалось загрузить схему
     * @return ITableScheme
     */
    public function getTable($name);

    /**
     * Помечает таблицу на удаление
     * @param string $name имя таблицы
     * @throws IException если не удалось загрузить схему
     * @return self
     */
    public function deleteTable($name);

    /**
     * Немедленно удаляет таблицу
     * @param string $name имя таблицы
     * @return bool
     */
    public function dropTable($name);

    /**
     * Немедленно очищает таблицу
     * @param string $name имя таблицы
     * @return bool
     */
    public function truncateTable($name);

    /**
     * Выключает индексирование данных в таблице. <br />
     * Используется совместно с enableKeys для ускорения множественных
     * операции вставки/изменения строк.
     * @param string $tableName имя таблицы
     * @return bool
     */
    public function disableKeys($tableName);

    /**
     * Включает индексирование данных в таблице. <br />
     * Используется совместно с disableKeys для ускорения множественных
     * операции вставки/изменения строк.
     * @param string $tableName имя таблицы
     * @return bool
     */
    public function enableKeys($tableName);

    /**
     * Выключает проверку внешних ключей.
     * @return bool
     */
    public function disableForeignKeysCheck();

    /**
     * Включает проверку внешних ключей.
     * @return bool
     */
    public function enableForeignKeysCheck();

    /**
     * Инициализирует транзакцию
     * @return self
     */
    public function startTransaction();

    /**
     * Фиксирует транзакцию
     * @return self
     */
    public function commitTransaction();

    /**
     * Откатывает транзакцию
     * @return self
     */
    public function rollbackTransaction();

    /**
     * Возвращает список имен всех таблиц в БД
     * @return array
     */
    public function getTableNames();

    /**
     * Проверяет существование таблицы в БД
     * @param string $name имя таблицы
     * @return bool
     */
    public function getTableExists($name);

    /**
     * Добавляет новую таблицу или возвращает существующую
     * @param string $name имя таблицы
     * @return ITableScheme
     */
    public function addTable($name);

    /**
     * Возвращает список добавленных (новых) таблиц
     * @return ITableScheme[]
     */
    public function getNewTables();

    /**
     * Возвращает список модифицированных таблиц
     * @return ITableScheme[]
     */
    public function getModifiedTables();

    /**
     * Открывает соединение с сервером, если оно еще не было открыто
     */
    public function ping();

    /**
     * Квотирует строку для безопасного использования в sql-запросах.
     * Внимание: Этот метод предназначен только для строк, обрамляет
     * всю строку в одиночные кавычки.
     * @param string $string строка
     * @return string
     */
    public function quote($string);

    /**
     * Возвращает выражение для конкатенации двух строк.
     * Для корректного результаты имена столбцов должны быть экранированы, а простые строки квотированы.
     * @param string $string1
     * @param string $string2
     * @return string
     */
    public function concat($string1, $string2);

    /**
     * Возвращает PDO - объект и устанавливает соединение с сервером БД,
     * если оно еще не было установлено
     * @internal
     * @return PDO
     */
    public function getPDO();

    /**
     * Возвращает PDO тип, соответствующий php-типу
     * @param string $phpType http://ru2.php.net/manual/en/function.gettype.php
     * @return int PDO::PARAM_{type}. Если нет соответсвующего типа, вернет PDO::PARAM_STR
     */
    public function getPdoType($phpType);

    /**
     * Сбросывает кэш схем, заставляя драйвер перечитать структуру таблиц
     * при следующем обращении
     * @return self
     */
    public function reset();

    /**
     * Применяет изменения схем таблиц, а так же измененных
     * свойств БД
     * @throws IException если в процессе миграции
     * произошла какая-либо ошибка
     * @return bool если изменения были и они успешно применены
     */
    public function applyMigrations();

    /**
     * Возвращает список запросов для изменения схем таблиц,
     * свойств БД
     * @throws IException если в процессе генерации запросов
     * произошла ошибка
     * @return array массив запросов для миграции
     */
    public function getMigrationQueries();

    /**
     * Возвращает внутренний тип столбца в конкретной c учетом его размера
     * @param string $internalType текущий внутренний тип столбца
     * @param int $size размер столбца
     * @return string
     */
    public function getColumnInternalTypeBySize($internalType, $size);

    /**
     * Возвращает список дефолтных опций для типа столбца
     * @param string $type тип столбца
     * @throws NonExistentEntityException если типа не существует
     * @return array
     */
    public function getColumnTypeOptions($type);

    /**
     * Возвращает внутренний тип столбца драйвера по абстрактному типу
     * @param $type абстрактный тип
     * @throws IException если невозможно получить тип
     * @return string
     */
    public function getColumnInternalType($type);

    /**
     * Возвращает экранированное имя таблицы для построения запросов
     * @param string $name имя таблицы
     * @return string
     */
    public function sanitizeTableName($name);

    /**
     * Возвращает экранированное имя столбца таблицы для построения запросов
     * @param string $name имя столбца
     * @return string
     */
    public function sanitizeColumnName($name);

    /**
     * Строит и возвращает sql-запрос на выборку данных
     * @param ISelectBuilder $query select-запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    public function buildSelectQuery(ISelectBuilder $query);

    /**
     * Строит и возвращает sql-запрос на получение количества записей, удовлетворяюших SELECT-запросу
     * @param ISelectBuilder $query select-запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    public function buildSelectFoundRowsQuery(ISelectBuilder $query);

    /**
     * Строит и возвращает sql-запрос на обновление данных.
     * @param IUpdateBuilder $query update-запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    public function buildUpdateQuery(IUpdateBuilder $query);

    /**
     * Строит и возвращает sql-запрос на вставку данных.
     * @param IInsertBuilder $query insert-запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    public function buildInsertQuery(IInsertBuilder $query);

    /**
     * Строит и возвращает sql-запрос на удаление данных
     * @param IDeleteBuilder $query delete-запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    public function buildDeleteQuery(IDeleteBuilder $query);

    /**
     * Строит и возвращает sql-запрос на очистку данных в таблице
     * @param string $tableName имя таблицы
     * @return string
     */
    public function buildTruncateQuery($tableName);

    /**
     * Строит и возвращает sql-запрос на удаление таблицы
     * @param string $tableName имя таблицы
     * @return string
     */
    public function buildDropQuery($tableName);

    /**
     * Строит и возвращает sql-запрос на выключение индексирования данных в таблице. <br />
     * Может ускорить множественные операции вставки/изменения строк.
     * @param string $tableName имя таблицы
     * @return string
     */
    public function buildDisableKeysQuery($tableName);

    /**
     * Построить sql-запрос на включение индексирования данных в таблице. <br />
     * Используется совместно с методом buildDisableKeysQuery для ускорения множественных
     * операций вставки/изменения строк.
     * @param string $tableName имя таблицы
     * @return string
     */
    public function buildEnableKeysQuery($tableName);

    /**
     * Строит и возвращает sql-запрос на выключение проверки внешних ключей. <br />
     * Используется при непоследовательном удалении, добавлении и редактировании записей во избежании конфликтов.
     * @return string
     */
    public function buildDisableForeignKeysQuery();

    /**
     * Строит и возвращает sql-запрос на включение проверки внешних ключей.
     * @return string
     */
    public function buildEnableForeignKeysQuery();

    /**
     * Выполняет прямой запрос на выборку данных.<br />
     * Внимание: Можно использовать только для тестов, для реализации драйвера БД,
     * для быстрых операций с БД не в "коробочных" целях.
     * За безопасностью запроса должен следить использующий код.
     * @internal
     * @param string $sql sql-запрос
     * @param array $params массив параметров для подготовленных запросов
     * @throws RuntimeException если в процессе выполнения запроса произошли ошибки
     * @return PDOStatement
     */
    public function select($sql, array $params = null);

    /**
     * Выполняет прямой запрос на модификацию данных.<br >
     * Внимание: Можно использовать только для тестов, для реализации драйвера БД,
     * для быстрых операций с БД не в "коробочных" целях.
     * За безопасностью запроса должен следить использующий код.
     * @internal
     * @param string $sql sql-запрос
     * @param array $params массив параметров для подготовленных запросов
     * @throws RuntimeException если в процессе выполнения запроса произошли ошибки
     * @return int количество затронутых запросом строк
     */
    public function modify($sql, array $params = null);

    /**
     * Подготавливает запрос
     * @internal
     * @param string $sql шаблон запроса
     * @param IQueryBuilder|null $queryBuilder билдер запроса
     * @event IConnection::EVENT_BEFORE_PREPARE_QUERY
     * @throws RuntimeException если не удалось подготовить запрос
     * @return PDOStatement
     */
    public function prepareStatement($sql, IQueryBuilder $queryBuilder = null);

    /**
     * Выполняет подготовленный запрос
     * @internal
     * @param PDOStatement $preparedStatement
     * @param IQueryBuilder|null $queryBuilder билдер запроса
     * @event IConnection::EVENT_AFTER_EXECUTE_QUERY
     * @throws RuntimeException если в процессе выполнения запроса произошли ошибки
     * @return PDOStatement
     */
    public function executeStatement(PDOStatement $preparedStatement, IQueryBuilder $queryBuilder = null);

}

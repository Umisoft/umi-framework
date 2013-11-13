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
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\cluster\IConnection;
use umi\dbal\exception\DomainException;
use umi\dbal\exception\NonexistentEntityException;
use umi\dbal\exception\RuntimeException;
use umi\event\TEventObservant;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\log\ILoggerAware;
use umi\log\TLoggerAware;

/**
 * Базовый класс драйвера БД.
 */
abstract class BaseDriver implements IDbDriver, ILocalizable, ILoggerAware
{

    use TLocalizable;
    use TEventObservant;
    use TLoggerAware;

    /**
     * @var array $columnTypes список опций типов колонок
     */
    public $columnTypes = [];
    /**
     * @var string $dsn строка для подключения к БД (http://ru2.php.net/manual/en/pdo.construct.php)
     */
    public $dsn = '';
    /**
     * @var string $user имя пользователя
     */
    public $user = '';
    /**
     * @var string $password пароль
     */
    public $password = '';
    /**
     * @var string $charset кодировка соединения с БД, по-умолчанию utf-8
     */
    public $charset = 'utf8';
    /**
     * @var ITableFactory $tableFactory фабрика, создающая таблицы и их сущности
     */
    protected $tableFactory;
    /**
     * @var PDO $pdo PDO {@link http://ru2.php.net/manual/en/class.pdo.php}
     */
    private $pdo;
    /**
     * @var ITableScheme[] $tableSchemes загруженные схемы, кэш
     */
    private $tableSchemes = [];
    /**
     * @var array $tableNames список имен таблиц, кэш
     */
    private $tableNames;

    /**
     * Загружает схему таблицы с именем $name.
     * Должен быть реализован в конкретном драйвере.
     * @param string $name имя таблицы
     * @throws RuntimeException если не удалось загрузить схему
     * @return ITableScheme схема таблицы
     */
    abstract protected function loadTableScheme($name);

    /**
     * Загружает список имен таблиц.
     * Должен быть реализован в конкретном драйвере.
     * @throws RuntimeException если не удалось загрузить список
     * @return array
     */
    abstract protected function loadTableNames();

    /**
     * Инициализирует PDO, используя специфику драйвера.
     * Может быть переопределен в конкретном драйвере.
     * @param PDO $pdo
     */
    protected function initPDOInstance(PDO $pdo)
    {
    }

    /**
     * @param ITableFactory $tableFactory фабрика сущностей таблиц
     */
    function __construct(ITableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function sanitizeTableName($name)
    {
        $name = str_replace('`', '``', $name);
        $name = addcslashes($name, "\r\n\000\\\032");

        return '`' . $name . '`';
    }

    /**
     * {@inheritdoc}
     */
    public function sanitizeColumnName($name)
    {
        if (strpos($name, '.') === false) {
            return $this->sanitizeTableName($name);
        }

        $parts = explode('.', $name);
        foreach ($parts as &$part) {
            $part = $this->sanitizeTableName($part);
        }

        return implode('.', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($name)
    {
        if (isset($this->tableSchemes[$name])) {
            return $this->tableSchemes[$name];
        }
        $this->tableSchemes[$name] = $this->loadTableScheme($name);

        return $this->tableSchemes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTable($name)
    {
        $table = $this->getTable($name);
        $table->setIsDeleted(true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dropTable($name)
    {
        $query = $this->buildDropQuery($name);
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function truncateTable($name)
    {
        $query = $this->buildTruncateQuery($name);
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disableKeys($tableName)
    {
        $query = $this->buildDisableKeysQuery($tableName);
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enableKeys($tableName)
    {
        $query = $this->buildEnableKeysQuery($tableName);
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disableForeignKeysCheck()
    {
        $query = $this->buildDisableForeignKeysQuery();
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enableForeignKeysCheck()
    {
        $query = $this->buildEnableForeignKeysQuery();
        try {
            $this->modify($query);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startTransaction()
    {
        //TODO возможно, стоит выставить уровень изоляции
        $this->getPDO()
            ->beginTransaction();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
        $this->getPDO()
            ->commit();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
        $this->getPDO()
            ->rollBack();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames()
    {
        if (is_array($this->tableNames)) {
            return $this->tableNames;
        }
        $this->tableNames = $this->loadTableNames();

        return $this->tableNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableExists($name)
    {
        try {
            return $this->getTable($name) instanceof ITableScheme;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addTable($name)
    {
        if ($this->getTableExists($name)) {
            return $this->getTable($name);
        }

        $this->tableSchemes[$name] = $scheme = $this->createTableSchemeInstance($name);
        $scheme->setIsNew(true);

        return $scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTables()
    {
        $result = [];

        foreach ($this->tableSchemes as $name => $scheme) {
            if ($scheme->getIsNew()) {
                $result[$name] = $scheme;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedTables()
    {
        $result = [];

        foreach ($this->tableSchemes as $name => $scheme) {
            if ($scheme->getIsModified() && !$scheme->getIsNew()) {
                $result[$name] = $scheme;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ping()
    {
        if (!$this->pdo) {
            $this->open();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string)
    {
        $string = (string) $string;

        if (($quotedValue = $this->getPDO()
                ->quote($string, PDO::PARAM_STR)) !== false
        ) {
            return $quotedValue;
        }
        // если драйвер не поддерживает quote, экранируем своими силами
        $quotedValue = str_replace('\'', '\'\'', $string);
        $quotedValue = '\'' . addcslashes($quotedValue, "\r\n\000\\\032") . '\'';

        return $quotedValue;
    }

    /**
     * {@inheritdoc}
     */
    public function concat($string1, $string2)
    {
        return 'CONCAT(' . $string1 . ',' . $string2 . ')';
    }

    /**
     * {@inheritdoc}
     */
    public function select($sql, array $params = null)
    {
        try {
            if (is_array($params) && count($params)) {
                $preparedStatement = $this->prepareStatement($sql);
                $preparedStatement->execute($params);

                return $preparedStatement;
            }

            return $this->getPDO()
                ->query($sql);
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Select query error (dsn: "{dsn}").',
                ['dsn' => $this->dsn]
            ), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify($sql, array $params = null)
    {
        try {
            $preparedStatement = $this->prepareStatement($sql);
            $preparedStatement->execute($params);
            $count = $preparedStatement->rowCount();
            $preparedStatement->closeCursor();

            return $count;
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Modify query "{sql}" error (dsn: "{dsn}").',
                ['sql' => $sql, 'dsn' => $this->dsn]
            ), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPDO()
    {
        $this->ping();

        return $this->pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function getPdoType($phpType)
    {
        switch (strtolower($phpType)) {
            case 'string':
            case 'str':
                return PDO::PARAM_STR;
            case 'integer':
            case 'int':
                return PDO::PARAM_INT;
            case 'boolean':
            case 'bool':
                return PDO::PARAM_BOOL;
            case 'blob':
            case 'lob':
                return PDO::PARAM_LOB;
            case 'null':
                return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->tableNames = null;
        $this->tableSchemes = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applyMigrations()
    {
        $queries = $this->getMigrationQueries();
        if (empty($queries)) {
            return false;
        }
        foreach ($queries as $query) {
            $this->modify($query);
        }

        $this->reset();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationQueries()
    {
        $result = [];

        foreach ($this->tableSchemes as $tableScheme) {
            $queries = $tableScheme->getMigrationQueries();
            if (!empty($queries)) {
                $result = array_merge($result, $queries);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnTypeOptions($type)
    {
        if (!isset($this->columnTypes[$type])) {
            throw new NonexistentEntityException($this->translate(
                'Column type {type} not found.',
                ['type' => $type]
            ));
        }

        return $this->columnTypes[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnInternalType($type)
    {
        $options = $this->getColumnTypeOptions($type);

        if (!isset($options[IColumnScheme::OPTION_TYPE])) {
            throw new RuntimeException($this->translate(
                'Internal type for column type {type} is not defined.',
                ['type' => $type]
            ));
        }

        return $options[IColumnScheme::OPTION_TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function buildTruncateQuery($tableName)
    {
        $tableName = $this->sanitizeTableName($tableName);

        return 'DELETE FROM ' . $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDropQuery($tableName)
    {
        $tableName = $this->sanitizeTableName($tableName);

        return 'DROP TABLE IF EXISTS ' . $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStatement($sql, IQueryBuilder $queryBuilder = null)
    {
        $this->fireEvent(
            IConnection::EVENT_BEFORE_PREPARE_QUERY,
            array(
                'queryBuilder' => $queryBuilder,
                'sql'          => $sql
            )
        );

        try {
            $preparedStatement = $this->getPDO()
                ->prepare($sql);
        } catch (\Exception $e) {
            $params = !is_null($queryBuilder) ? var_export($queryBuilder->getPlaceholderValues(), true) : null;
            throw new RuntimeException($this->translate(
                'Cannot prepare query "{query}" with params: {params}.',
                [
                    'query'  => $sql,
                    'params' => $params
                ]
            ), 0, $e);
        }

        return $preparedStatement;
    }

    /**
     * {@inheritdoc}
     */
    public function executeStatement(PDOStatement $preparedStatement, IQueryBuilder $queryBuilder = null)
    {
        $params = !is_null($queryBuilder) ? var_export($queryBuilder->getPlaceholderValues(), true) : null;

        try {
            $preparedStatement->execute();
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot execute query "{query}" with params: {params}.',
                [
                    'query'  => $preparedStatement->queryString,
                    'params' => $params
                ]
            ), 0, $e);
        }

        $this->fireEvent(
            IConnection::EVENT_AFTER_EXECUTE_QUERY,
            array(
                'queryBuilder'      => $queryBuilder,
                'preparedStatement' => $preparedStatement
            )
        );

        $this->trace(
            'Execute query: {sql} with params {params}.',
            ['sql' => $preparedStatement->queryString, 'params' => $params]
        );
    }

    /**
     * Создает и конфигурирует новый экземпляр PDO
     * @return PDO
     */
    protected function createPDOWrapper()
    {
        $pdo = new PDO($this->dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        $this->initPDOInstance($pdo);

        return $pdo;
    }

    /**
     * Открывает соединение c сервером БД
     * @throws RuntimeException если не удалось открыть соединение
     */
    protected function open()
    {
        if (empty($this->dsn)) {
            throw new RuntimeException($this->translate(
                'Failed to open DB connection: dsn string cannot be empty.'
            ));
        }

        try {
            $this->pdo = $this->createPDOWrapper();
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Failed to open DB connection to "{dsn}".',
                ['dsn' => $this->dsn]
            ), 0, $e);
        }
    }

    /**
     * Закрывает соединение с сервером
     */
    protected function close()
    {
        $this->pdo = null;
    }

    /**
     * Создает и возвращает схему таблицы
     * @param string $name имя таблицы
     * @throws DomainException если не удалось создать таблицу
     * @return ITableScheme
     */
    protected function createTableSchemeInstance($name)
    {
        return $this->tableFactory->createTable($name, $this);
    }
}

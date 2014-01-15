<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache\engine;

use umi\cache\exception\InvalidArgumentException;
use umi\dbal\builder\DeleteBuilder;
use umi\dbal\builder\InsertBuilder;
use umi\dbal\builder\SelectBuilder;
use umi\dbal\cluster\IDbClusterAware;
use umi\dbal\cluster\server\IServer;
use umi\dbal\cluster\TDbClusterAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Механизм хранения кэша в простой таблице БД.
 */
class Db implements ICacheEngine, IDbClusterAware, ILocalizable
{

    use TDbClusterAware;
    use TLocalizable;

    /**
     * @var array $options список опций в формате:
     * [
     *        'table' => [
     *            'tableName' => $tableName,
     *            'keyColumnName' => $keyColumnName,
     *            'valueColumnName' => $valueColumnName,
     *            'expireColumnName' => $expireColumnName
     *        ],
     *        'serverId' => $serverId
     * ]
     */
    protected $options;

    /**
     * @var IServer $server сервер для хранения кеша
     */
    private $server;
    /**
     * @var string $tableName имя таблицы в бд, где хранится кэш
     */
    private $tableName;
    /**
     * @var string $keyColumnName имя стобца в таблице для хранения ключей кеша
     */
    private $keyColumnName;
    /**
     * @var string $valueColumnName имя стобца в таблице для хранения значений кеша
     */
    private $valueColumnName;
    /**
     * @var string $expireColumnName имя стобца в таблице для хранения метки времени инвалидации кеша
     */
    private $expireColumnName;
    /**
     * @var DeleteBuilder $preparedDeleteById подготовленный запрос на удаление
     */
    private $preparedDeleteById;
    /**
     * @var InsertBuilder $preparedInsert подготовленный запрос на вставку
     */
    private $preparedInsert;
    /**
     * @var InsertBuilder $preparedInsertUpdate подготовленный запрос на вставку/обновление
     */
    private $preparedInsertUpdate;
    /**
     * @var SelectBuilder $preparedSelect подготовленный запрос на выборку
     */
    private $preparedSelect;
    /**
     * @var SelectBuilder $preparedMultiSelect подготовленный запрос на выборку нескольких записей
     */
    private $preparedMultiSelect;

    /**
     * Конструктор.
     * @param array $options список опций в формате:
     * [
     *        'table' => [
     *            'tableName' => $tableName,
     *            'keyColumnName' => $keyColumnName,
     *            'valueColumnName' => $valueColumnName,
     *            'expireColumnName' => $expireColumnName
     *        ],
     *        'serverId' => $serverId
     * ]
     */
    public function __construct(array $options)
    {

        $this->options = $this->parseConfig($options);

        $this->tableName = $this->options['table']['tableName'];
        $this->keyColumnName = $this->options['table']['keyColumnName'];
        $this->valueColumnName = $this->options['table']['valueColumnName'];
        $this->expireColumnName = $this->options['table']['expireColumnName'];

    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0)
    {

        $ttl = $ttl > 0 ? $ttl + time() : 0;

        $insert = $this->getPreparedInsert(true);
        $insert
            ->bindString(':id', $key)
            ->bindBlob(':value', $value)
            ->bindInt(':expire', $ttl);

        return $insert->execute()->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = 0)
    {

        $ttl = $ttl > 0 ? $ttl + time() : 0;

        $insert = $this->getPreparedInsertUpdate();
        $insert
            ->bindString(':id', $key)
            ->bindBlob(':value', $value)
            ->bindInt(':expire', $ttl)
            ->bindBlob(':newValue', $value)
            ->bindInt(':newExpire', $ttl);

        return $insert->execute()->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {

        $select = $this->getPreparedSelect()
            ->bindString(':id', $key)
            ->bindInt(':expire', time())
            ->bindInt(':zero', 0);

        $statement = $select->execute();
        $queryResult = $statement
            ->fetch();

        // PDO sqlite requires to unlock cursor after partial fetch
        $statement->closeCursor();

        $result = $queryResult ? $queryResult[$this->valueColumnName] : false;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $keys)
    {
        $result = array_combine($keys, array_fill(0, count($keys), false));

        $select = $this->getPreparedMultiSelect()
            ->bindArray(':ids', $keys)
            ->bindInt(':expire', time())
            ->bindInt(':zero', 0);
        $queryResult = $select->execute()
            ->fetchAll();

        foreach ($queryResult as $valueInfo) {
            $result[$valueInfo[$this->keyColumnName]] = $valueInfo[$this->valueColumnName];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $delete = $this->getPreparedDeleteById();
        $delete->bindString(':id', $key);

        return $delete->execute()->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $connection = $this
            ->getServer()
            ->getConnection();

        return $connection->exec(
            $connection
                ->getDatabasePlatform()
                ->getTruncateTableSQL($this->tableName)
        ) > 0;
    }

    /**
     * Удаляет ключи, время жизни которых истекло
     * @return bool успех операции
     */
    protected function garbageCollector()
    {
        return $this->getServer()
            ->delete($this->tableName)
            ->where()
            ->expr($this->expireColumnName, '>', ':zero')
            ->expr($this->expireColumnName, '<', ':expire')
            ->bindInt(':expire', time())
            ->bindInt(':zero', 0)
            ->execute()
            ->rowCount() > 0;
    }

    /**
     * Проверяет и возвращает опции для настройки
     * @param array $options
     * @throws InvalidArgumentException при неверных опциях
     * @return array
     */
    protected function parseConfig(array $options)
    {
        if (!isset($options['table']) || empty($options['table'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find table settings in configuration.'
            ));
        }

        if (!is_array($options['table'])) {
            throw new InvalidArgumentException($this->translate(
                'Table settings should be an array or Traversable.'
            ));
        }

        if (!isset($options['table']['tableName']) || empty($options['table']['tableName'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find table name in configuration.'
            ));
        }
        if (!isset($options['table']['keyColumnName']) || empty($options['table']['keyColumnName'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find key column name in configuration.'
            ));
        }

        if (!isset($options['table']['valueColumnName']) || empty($options['table']['valueColumnName'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find value column name in configuration.'
            ));
        }

        if (!isset($options['table']['expireColumnName']) || empty($options['table']['expireColumnName'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find expire column name in configuration.'
            ));
        }

        return $options;
    }

    /**
     * Возвращает сервер БД, на котором хранится кеш.
     * @return IServer
     */
    private function getServer()
    {
        if ($this->server) {
            return $this->server;
        }
        if (isset($this->options['serverId']) && !empty($this->options['serverId'])) {
            return $this->server = $this->getDbCluster()
                ->getServer($this->options['serverId']);
        }

        return $this->server = $this->getDbCluster()
            ->getMaster();
    }

    /**
     * Возвращает подготовленный DELETE для удаления кэша по ключу
     * по идентификаторам
     * @return DeleteBuilder
     */
    private function getPreparedDeleteById()
    {
        if (is_null($this->preparedDeleteById)) {
            $this->preparedDeleteById = $this->getServer()
                ->delete($this->tableName)
                ->where()
                ->expr($this->keyColumnName, '=', ':id');
        }

        return $this->preparedDeleteById;
    }

    /**
     * Возвращает подготовленный INSERT для добавления ключа в кэш
     * @return InsertBuilder
     */
    private function getPreparedInsert()
    {
        if (is_null($this->preparedInsert)) {
            $this->preparedInsert = $this->getServer()
                ->insert($this->tableName, true)
                ->set($this->keyColumnName, ':id')
                ->set($this->valueColumnName, ':value')
                ->set($this->expireColumnName, ':expire');
        }

        return $this->preparedInsert;
    }

    /**
     * Возвращает подготовленный INSERT ON DUPLICATE KEY UPDATE
     * для добавления или обновления ключа в кэше
     * @return InsertBuilder
     */
    private function getPreparedInsertUpdate()
    {
        if (is_null($this->preparedInsertUpdate)) {
            $this->preparedInsertUpdate = $this->getServer()
                ->insert($this->tableName)
                ->set($this->keyColumnName, ':id')
                ->set($this->valueColumnName, ':value')
                ->set($this->expireColumnName, ':expire')
                ->onDuplicateKey($this->keyColumnName)
                ->set($this->valueColumnName, ':newValue')
                ->set($this->expireColumnName, ':newExpire');
        }

        return $this->preparedInsertUpdate;
    }

    /**
     * Возвращает подготовленный SELECT для выборки значения из кэша по ключу
     * @return SelectBuilder
     */
    private function getPreparedSelect()
    {
        if (is_null($this->preparedSelect)) {
            $this->preparedSelect = $this->getServer()
                ->select()
                ->select($this->valueColumnName)
                ->from($this->tableName)
                ->where('AND')
                ->expr($this->keyColumnName, '=', ':id')
                ->begin('OR')
                ->expr($this->expireColumnName, '=', ':zero')
                ->expr($this->expireColumnName, '>', ':expire')
                ->end();
        }

        return $this->preparedSelect;
    }

    /**
     * Возвращает подготовленный SELECT для выборки значений из кэша по ключам
     * @return SelectBuilder
     */
    private function getPreparedMultiSelect()
    {
        if (is_null($this->preparedMultiSelect)) {
            $this->preparedMultiSelect = $this->getServer()
                ->select()
                ->select([$this->keyColumnName, $this->valueColumnName])
                ->from($this->tableName)
                ->where('AND')
                ->expr($this->keyColumnName, 'in', ':ids')
                ->begin('OR')
                ->expr($this->expireColumnName, '=', ':zero')
                ->expr($this->expireColumnName, '>', ':expire')
                ->end();
        }

        return $this->preparedMultiSelect;
    }
}

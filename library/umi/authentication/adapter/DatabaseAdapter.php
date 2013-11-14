<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\adapter;

use umi\authentication\exception\InvalidArgumentException;
use umi\authentication\result\AuthResult;
use umi\authentication\result\IAuthResult;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\cluster\IConnection;
use umi\dbal\cluster\IDbCluster;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Адаптер базы данных для аутентификации.
 */
class DatabaseAdapter implements IAuthAdapter, ILocalizable
{
    /** Имя таблицы */
    const OPTION_TABLE = 'table';
    /** Колонки содержащие логин пользователя */
    const OPTION_LOGIN_COLUMNS = 'loginColumns';
    /** Колонка содержащая пароль пользователя */
    const OPTION_PASSWORD_COLUMN = 'passwordColumn';

    use TLocalizable;

    /**
     * @var string $table таблица
     */
    protected $table;
    /**
     * @var array $usernameColumn имя пользователя
     */
    protected $loginColumns = [];
    /**
     * @var string $passwordColumn пароль
     */
    protected $passwordColumn;
    /**
     * @var IConnection $connection соединение с БД
     */
    protected $connection = null;

    /**
     * Инициализация подключения.
     * @param array $options
     * @param IDbCluster $connection соединение с БД
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [], IDbCluster $connection)
    {
        if (!isset($options[self::OPTION_TABLE]) ||
            !isset($options[self::OPTION_LOGIN_COLUMNS]) ||
            !isset($options[self::OPTION_PASSWORD_COLUMN])) {

            throw new InvalidArgumentException($this->translate(
                'Options "table", "loginColumns", "passwordColumn" is required.'
            ));
        }

        if (!is_array($this->loginColumns)) {
            throw new InvalidArgumentException($this->translate(
                'Option "loginColumns" should be an array.'
            ));
        }

        $this->table = $options[self::OPTION_TABLE];
        $this->loginColumns = $options[self::OPTION_LOGIN_COLUMNS];
        $this->passwordColumn = $options[self::OPTION_PASSWORD_COLUMN];

        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        $select = $this->connection->select()
            ->from($this->table);

        $where = $select->where(IExpressionGroup::MODE_OR);

        foreach ($this->loginColumns as $loginColumn) {
            $where->bindColumnString($loginColumn, $username);
        }

        $result = $select->execute();

        if ($result->countRows() != 1) {
            return new AuthResult(IAuthResult::WRONG_USERNAME);
        }

        $entity = $result->fetch(\PDO::FETCH_ASSOC);

        if ($entity[$this->passwordColumn] != $password) {
            return new AuthResult(IAuthResult::WRONG_PASSWORD);
        }

        return new AuthResult(
            IAuthResult::SUCCESSFUL,
            $entity
        );
    }
}
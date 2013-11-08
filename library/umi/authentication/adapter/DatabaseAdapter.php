<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\adapter;

use umi\authentication\exception\RuntimeException;
use umi\authentication\result\IAuthenticationResultAware;
use umi\authentication\result\IAuthResult;
use umi\authentication\result\TAuthenticationResultAware;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\cluster\IConnection;
use umi\dbal\cluster\IDbCluster;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Адаптер базы данных для аутентификации.
 * todo: refactor?
 */
class DatabaseAdapter implements IAuthAdapter, ILocalizable, IAuthenticationResultAware
{

    use TLocalizable;
    use TAuthenticationResultAware;

    /**
     * @var string $serverId идентификатор сервера
     */
    public $serverId;
    /**
     * @var ISelectBuilder $select запрос
     */
    public $select;
    /**
     * @var string $table таблица
     */
    public $table = 'users';
    /**
     * @var string $usernameColumn имя пользователя
     */
    public $usernameColumn = 'email';
    /**
     * @var string $passwordColumn пароль
     */
    public $passwordColumn = 'password';
    /**
     * @var IConnection $connection соединение с БД
     */
    protected $connection = null;

    /**
     * Инициализация подключения.
     * @param IDbCluster $connection соединение с БД
     */
    public function __construct(IDbCluster $connection)
    {
        $this->connection = $connection;

        if ($this->serverId) {
            $this->connection = $connection->getServer($this->serverId);
        }

        $this->select = $this->connection->select()
            ->from($this->table)
            ->where()
            ->expr($this->usernameColumn, '=', ':user')
            ->expr($this->passwordColumn, '=', ':password');
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        if (!$this->select instanceof ISelectBuilder) {
            throw new RuntimeException($this->translate(
                'Injected select should implement ISelectBuilder.'
            ));
        }

        $this->select
            ->bindString(':user', $username)
            ->bindString(':password', $password);

        $result = $this->select->execute();

        if ($result->countRows() == 0) {
            return $this->createAuthResult(IAuthResult::WRONG);
        }

        return $this->createAuthResult(
            IAuthResult::SUCCESSFUL,
            new \ArrayObject($result->fetch(\PDO::FETCH_OBJ), \ArrayObject::ARRAY_AS_PROPS)
        );
    }
}
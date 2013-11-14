<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\adapter;

use umi\authentication\result\AuthResult;
use umi\authentication\result\IAuthResult;

/**
 * Простой адаптер авторизации с помощью списка пользоватлей с паролями.
 */
class SimpleAdapter implements IAuthAdapter
{
    /** Список пользователей в формате [name => password] */
    const OPTION_ALLOWED_LIST = 'allowed';

    /**
     * @var array $allowed массив разрешенных пользователей, вида username => password
     */
    protected $allowed = [];

    public function __construct(array $options = [])
    {
        $this->allowed = isset($options[self::OPTION_ALLOWED_LIST]) ? $options[self::OPTION_ALLOWED_LIST] : $this->allowed;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        if (isset($this->allowed[$username]) && $this->allowed[$username] == $password) {
            return new AuthResult(IAuthResult::SUCCESSFUL, $username);
        } elseif (isset($this->allowed[$username])) {
            return new AuthResult(IAuthResult::WRONG_PASSWORD);
        } else {
            return new AuthResult(IAuthResult::WRONG_USERNAME);
        }
    }
}
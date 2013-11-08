<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\adapter;

use umi\authentication\result\IAuthenticationResultAware;
use umi\authentication\result\IAuthResult;
use umi\authentication\result\TAuthenticationResultAware;

/**
 * Простой адаптер авторизации с помощью списка пользоватлей с паролями.
 */
class SimpleAdapter implements IAuthAdapter, IAuthenticationResultAware
{

    use TAuthenticationResultAware;

    /**
     * @var array $allowed массив разрешенных пользователей, вида username => password
     */
    public $allowed = [];

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        if (isset($this->allowed[$username]) && $this->allowed[$username] == $password) {
            return $this->createAuthResult(IAuthResult::SUCCESSFUL, $username);
        } elseif (isset($this->allowed[$username])) {
            return $this->createAuthResult(IAuthResult::WRONG_PASSWORD);
        } else {
            return $this->createAuthResult(IAuthResult::WRONG_USERNAME);
        }
    }
}
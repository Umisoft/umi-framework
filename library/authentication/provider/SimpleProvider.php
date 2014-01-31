<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\provider;

/**
 * Класс базовой (transparent) авторизации.
 */
class SimpleProvider implements IAuthProvider
{
    /**
     * @var string $username имя пользователя
     */
    protected $username = '';
    /**
     * @var string $password пароль
     */
    protected $password = '';

    /**
     * Конструктор.
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->username = $login;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return [$this->username, $this->password];
    }
}
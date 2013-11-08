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
    public $username;
    /**
     * @var string $password пароль
     */
    public $password;

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        if ($this->username && $this->password) {
            return [$this->username, $this->password];
        } else {
            return false;
        }
    }
}
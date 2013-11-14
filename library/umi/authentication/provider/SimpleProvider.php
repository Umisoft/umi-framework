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
    /** Опция для установки имени пользователя */
    const OPTION_USERNAME = 'username';
    /** Опция для установки пароля */
    const OPTION_PASSWORD = 'password';

    /**
     * @var string $username имя пользователя
     */
    protected $username = '';
    /**
     * @var string $password пароль
     */
    protected $password = '';

    public function __construct(array $options = [])
    {
        $this->username = isset($options[self::OPTION_USERNAME]) ? $options[self::OPTION_USERNAME] : $this->username;
        $this->password = isset($options[self::OPTION_PASSWORD]) ? $options[self::OPTION_PASSWORD] : $this->password;
    }

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
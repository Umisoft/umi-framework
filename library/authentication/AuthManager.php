<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication;

use umi\authentication\adapter\IAuthAdapter;
use umi\authentication\exception\RuntimeException;
use umi\authentication\provider\IAuthProvider;
use umi\authentication\result\AuthResult;
use umi\authentication\result\IAuthResult;
use umi\authentication\storage\IAuthStorage;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс менеджера аутентификации.
 */
class AuthManager implements IAuthManager, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $hashMethod метод для хэширования пароля
     */
    protected $hashMethod = self::HASH_NONE;
    /**
     * @var string $hashSalt соль для хэширования пароля
     */
    protected $hashSalt = '';
    /**
     * @var IAuthAdapter $adapter провайдер авторизации
     */
    protected $adapter;
    /**
     * @var IAuthStorage $storage хранилище данных авторизации
     */
    protected $storage;

    /**
     * Конструктор.
     * @param array $options опции менеджера аутентификации
     * @param IAuthAdapter $adapter адаптер аутентификации
     * @param IAuthStorage $storage хранилище аутентификации
     */
    public function __construct(array $options, IAuthAdapter $adapter, IAuthStorage $storage)
    {
        if (isset($options[self::OPTION_HASH_METHOD])) {
            $this->hashMethod = $options[self::OPTION_HASH_METHOD];
        }
        if (isset($options[self::OPTION_HASH_SALT])) {
            $this->hashSalt = $options[self::OPTION_HASH_SALT];
        }

        $this->adapter = $adapter;
        $this->storage = $storage;
    }

    /**
     * Возвращает хранилище авторизации.
     * @return IAuthStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(IAuthProvider $provider)
    {
        $credentials = $provider->getCredentials();

        if ($credentials) {
            if (count($credentials) < 2) {
                throw new RuntimeException($this->translate(
                    'Cannot get username and password.'
                ));
            }

            list($username, $password) = $credentials;
        } else {
            return new AuthResult(IAuthResult::WRONG_NO_CREDENTIALS);
        }

        if ($this->storage->hasIdentity()) {
            return new AuthResult(
                IAuthResult::ALREADY,
                $this->getStorage()->getIdentity()
            );
        }

        $result = $this->adapter->authenticate(
            $username,
            $this->getPasswordHash($password)
        );

        if ($result->isSuccessful()) {
            $this->storage->setIdentity($result->getIdentity());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return ($this->storage) && $this->storage->hasIdentity();
    }

    /**
     * {@inheritdoc}
     */
    public function forget()
    {
        $this->storage->clearIdentity();

        return $this;
    }

    /**
     * Возвращает хэш пароля.
     * @param string $password
     * @throws RuntimeException
     * @return string
     */
    protected function getPasswordHash($password)
    {
        switch ($this->hashMethod) {
            case self::HASH_NONE:
                return $password;
            case self::HASH_MD5:
                return md5($password . strval($this->hashSalt));
            case self::HASH_CRYPT:
                return crypt($password, $this->hashSalt);
            case self::HASH_SHA1:
                return sha1($password . strval($this->hashSalt));
            default:
                throw new RuntimeException($this->translate(
                    'Invalid authentication password hashing method.'
                ));
        }
    }
}

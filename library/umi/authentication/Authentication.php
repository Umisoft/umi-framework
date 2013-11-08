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
use umi\authentication\result\IAuthenticationResultAware;
use umi\authentication\result\IAuthResult;
use umi\authentication\result\TAuthenticationResultAware;
use umi\authentication\storage\IAuthStorage;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс менеджера аутентификации.
 */
class Authentication implements IAuthentication, ILocalizable, IAuthenticationResultAware
{

    use TAuthenticationResultAware;
    use TLocalizable;

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
     * @param IAuthAdapter $adapter адаптер авторизации
     * @param IAuthStorage $storage хранилище авторизации
     */
    public function __construct(IAuthAdapter $adapter, IAuthStorage $storage)
    {
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
            return $this->createAuthResult(IAuthResult::WRONG_NO_CREDENTIALS);
        }

        if ($this->storage->hasIdentity()) {
            return $this->createAuthResult(
                IAuthResult::ALREADY,
                $this->getStorage()
                    ->getIdentity()
            );
        }

        $result = $this->adapter->authenticate($username, $password);

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
}
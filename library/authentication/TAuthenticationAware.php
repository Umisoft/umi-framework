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
use umi\authentication\exception\RequiredDependencyException;
use umi\authentication\provider\IAuthProvider;
use umi\authentication\storage\IAuthStorage;

/**
 * Трейт для внедрения поддержки аутентификации.
 */
trait TAuthenticationAware
{
    /**
     * @var IAuthenticationFactory $_authTools инструменты работы с авторизацией
     */
    private $_authFactory;

    /**
     * Устанавливает фабрика аутентификации.
     * @param IAuthenticationFactory $authFactory фабрика
     * @return self
     */
    public final function setAuthenticationFactory(IAuthenticationFactory $authFactory)
    {
        $this->_authFactory = $authFactory;
    }

    /**
     * Возвращает сконфигурированный адаптер.
     * @param array $config конфигурация адаптера
     * @return IAuthAdapter
     */
    protected final function createAuthAdapter(array $config = [])
    {
        return $this->getAuthFactory()
            ->createAdapter($config);
    }

    /**
     * Возвращает сконфигурированный storage.
     * @param array $config конфигурация хранилища
     * @return IAuthStorage
     */
    protected final function createAuthStorage(array $config = [])
    {
        return $this->getAuthFactory()
            ->createStorage($config);
    }

    /**
     * Возвращает сконфигурированный провайдер.
     * @param string $type тип провайдера
     * @param array $constructorArgs аргументы конструктора провайдера
     * @return IAuthProvider
     */
    protected final function createAuthProvider($type, array $constructorArgs = [])
    {
        return $this->getAuthFactory()
            ->createProvider($type, $constructorArgs);
    }

    /**
     * Создает менеджер аутентификации.
     * @param IAuthAdapter $adapter адаптер аутентификации
     * @param IAuthStorage $storage хранилище аутентификации
     * @param array $options опции менеджера аутентификации
     * @return IAuthManager
     */
    protected final function createAuthManager(IAuthAdapter $adapter, IAuthStorage $storage, array $options = [])
    {
        return $this->getAuthFactory()
            ->createAuthManager($adapter, $storage, $options);
    }

    /**
     * Возвращает менеджер аутентификации с натройками по умолчанию.
     * @return IAuthManager
     */
    protected final function getDefaultAuthManager()
    {
        return $this->getAuthFactory()->getDefaultAuthManager();
    }

    /**
     * Возвращает инструменты для работы с аутентификацией.
     * @return IAuthenticationFactory
     * @throws RequiredDependencyException если инструменты для работы с аутентификацией не установлены
     */
    private function getAuthFactory()
    {
        if (!$this->_authFactory) {
            throw new RequiredDependencyException(sprintf(
                'Authentication factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_authFactory;
    }
}

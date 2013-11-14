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
use umi\authentication\provider\IAuthProvider;
use umi\authentication\storage\IAuthStorage;

/**
 * Интерфейс для внедрения поддержки авторизации.
 *
 */
interface IAuthenticationFactory
{
    /** Сессионное хранилище */
    const STORAGE_SESSION = 'session';
    /** Сессионное хранилище для объектов ORM */
    const STORAGE_ORM_SESSION = 'ormSession';
    /** Базовое хранилище */
    const STORAGE_SIMPLE = 'simple';

    /** Базовый провайдер авторизации */
    const PROVIDER_SIMPLE = 'simple';
    /** Провайдер Http авторизации */
    const PROVIDER_HTTP = 'http';

    /** Базовый адаптер */
    const ADAPTER_SIMPLE = 'simple';
    /** Адаптер БД */
    const ADAPTER_DATABASE = 'database';
    /** Адаптер ORM */
    const ADAPTER_ORM = 'orm';

    /**
     * Создает хранилище аутентификации.
     * @param string $type тип хранилища аутентификации
     * @param array $options опции хранилища
     * @return IAuthStorage
     */
    public function createStorage($type, array $options = []);

    /**
     * Создает адаптер аутентификации.
     * @param string $type тип провайдера
     * @param array $options опции адаптера
     * @return IAuthAdapter
     */
    public function createAdapter($type, array $options = []);

    /**
     * Создает провайдер аутентификации.
     * @param string $type тип провайдера
     * @param array $options опции провайдера
     * @return IAuthProvider
     */
    public function createProvider($type, array $options = []);

    /**
     * Создает менеджер аутентификации.
     * @param array $options опции менеджера аутентификации
     * @param IAuthAdapter $adapter адаптер аутентификации
     * @param IAuthStorage $storage хранилище аутентификации
     * @return mixed
     */
    public function createManager(array $options = [], IAuthAdapter $adapter = null, IAuthStorage $storage = null);
}

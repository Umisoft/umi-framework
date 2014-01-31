<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox\factory;

use umi\authentication\adapter\IAuthAdapter;
use umi\authentication\exception\InvalidArgumentException;
use umi\authentication\exception\OutOfBoundsException;
use umi\authentication\IAuthenticationFactory;
use umi\authentication\IAuthManager;
use umi\authentication\storage\IAuthStorage;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика сущностей аутентификации.
 */
class AuthenticationFactory implements IAuthenticationFactory, IFactory
{
    use TFactory;

    /**
     * @var string $managerClass класс менеджера аутентификации
     */
    public $managerClass = 'umi\authentication\AuthManager';
    /**
     * @var array $storageClasses классы хранилищ аутентификации
     */
    public $storageClasses = [
        self::STORAGE_SIMPLE  => 'umi\authentication\storage\SimpleStorage',
        self::STORAGE_SESSION => 'umi\authentication\storage\SessionStorage',
        self::STORAGE_ORM_SESSION => 'umi\authentication\storage\ORMSessionStorage',
    ];
    /**
     * @var array $adapterClasses классы адаптеров аутентификации
     */
    public $adapterClasses = [
        self::ADAPTER_SIMPLE   => 'umi\authentication\adapter\SimpleAdapter',
        self::ADAPTER_DATABASE => 'umi\authentication\adapter\DatabaseAdapter',
        self::ADAPTER_ORM => 'umi\authentication\adapter\ORMAdapter',
    ];
    /**
     * @var array $providerClasses классы провайдеров аутентификации
     */
    public $providerClasses = [
        self::PROVIDER_SIMPLE => 'umi\authentication\provider\SimpleProvider',
        self::PROVIDER_HTTP   => 'umi\authentication\provider\HttpProvider',
    ];

    /**
     * @var array $defaultAdapter настройки адаптера аутентификации по умолчанию
     */
    public $defaultAdapter = [
        'type'    => self::ADAPTER_SIMPLE,
        'options' => []
    ];
    /**
     * @var array $defaultStorage настройки хранилища аутентификации по умолчанию
     */
    public $defaultStorage = [
        'type'    => self::STORAGE_SIMPLE,
        'options' => []
    ];
    /**
     * @var array $defaultAuthManager настройки менеджера аутентификации по умолчанию
     */
    public $defaultAuthManager = [
        IAuthManager::OPTION_HASH_METHOD => IAuthManager::HASH_NONE,
        IAuthManager::OPTION_HASH_SALT => ''
    ];

    /**
     * @var IAuthManager $defaultAuthManagerInstance менеджер аутентификации с настройками по умолчанию
     */
    private $defaultAuthManagerInstance;

    /**
     * {@inheritdoc}
     */
    public function createStorage($type, array $options = [])
    {
        if (!$type) {
            throw new InvalidArgumentException($this->translate(
                'Storage type cannot be empty.'
            ));
        }

        if (!isset($this->storageClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Storage type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->getPrototype(
                $this->storageClasses[$type],
                ['umi\authentication\storage\IAuthStorage']
            )
            ->createInstance()
            ->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter($type, array $options = [])
    {
        if (!$type) {
            throw new InvalidArgumentException($this->translate(
                'Adapter type cannot be empty.'
            ));
        }

        if (!isset($this->adapterClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Adapter type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->getPrototype(
                $this->adapterClasses[$type],
                ['umi\authentication\adapter\IAuthAdapter']
            )
            ->createInstance([$options]);

    }

    /**
     * {@inheritdoc}
     */
    public function createProvider($type, array $constructorArgs = [])
    {
        if (!$type) {
            throw new InvalidArgumentException($this->translate(
                'Provider type cannot be empty.'
            ));
        }

        if (!isset($this->providerClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Provider type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->getPrototype(
                $this->providerClasses[$type],
                ['umi\authentication\provider\IAuthProvider']
            )
            ->createInstance($constructorArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function createAuthManager(IAuthAdapter $adapter, IAuthStorage $storage, array $options = [])
    {
        return $this->getPrototype(
                $this->managerClass,
                ['umi\authentication\IAuthManager']
            )
            ->createInstance([$options, $adapter, $storage]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAuthManager()
    {
        if (!$this->defaultAuthManagerInstance) {
            $authManagerOptions = $this->configToArray($this->defaultAuthManager, true);

            $adapterOptions = $this->configToArray($this->defaultAdapter['options'], true);
            $adapter = $this->createAdapter($this->defaultAdapter['type'], $adapterOptions);

            $storageOptions = $this->configToArray($this->defaultStorage['options'], true);
            $storage = $this->createStorage($this->defaultStorage['type'], $storageOptions);

            $this->defaultAuthManagerInstance = $this->getPrototype(
                $this->managerClass,
                ['umi\authentication\IAuthManager']
            )
                ->createSingleInstance([$authManagerOptions, $adapter, $storage]);
        }

        return $this->defaultAuthManagerInstance;
    }
}

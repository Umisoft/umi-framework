<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox\factory;

use umi\authentication\exception\OutOfBoundsException;
use umi\authentication\IAuthenticationFactory;
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
    public $managerClass = 'umi\authentication\Authentication';
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
     * @var array $manager опции менеджера аутентификации
     */
    public $manager = [];
    /**
     * @var array $adapter адаптер аутентификации по умолчанию
     */
    public $adapter = [
        'type'    => self::ADAPTER_SIMPLE,
        'options' => []
    ];
    /**
     * @var array $storage хранилище аутентификации по умолчанию
     */
    public $storage = [
        'type'    => self::STORAGE_SESSION,
        'options' => []
    ];

    /**
     * {@inheritdoc}
     */
    public function createStorage(array $config = [])
    {
        $config += $this->storage;
        $type = isset($config['type']) ? $config['type'] : '';
        $options = isset($config['options']) ? $config['options'] : [];

        if (!isset($this->storageClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Storage type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->createInstance(
            $this->storageClasses[$type],
            [$options],
            ['umi\authentication\storage\IAuthStorage']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter(array $config = [])
    {
        $config += $this->adapter;
        $type = isset($config['type']) ? $config['type'] : '';
        $options = isset($config['options']) ? $config['options'] : '';

        if (!isset($this->adapterClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Adapter type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->createInstance(
            $this->adapterClasses[$type],
            [],
            ['umi\authentication\adapter\IAuthAdapter'],
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createProvider($type, array $options = [])
    {
        if (!isset($this->providerClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Provider type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        return $this->createInstance(
            $this->providerClasses[$type],
            [],
            ['umi\authentication\provider\IAuthProvider'],
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createManager(array $config = [])
    {
        $storage = isset($config['storage']) ? $config['storage'] : [];
        $adapter = isset($config['adapter']) ? $config['adapter'] : [];

        return $this->createInstance(
            $this->managerClass,
            [$this->createAdapter($adapter), $this->createStorage($storage)],
            ['umi\authentication\IAuthentication'],
            $this->manager
        );
    }
}
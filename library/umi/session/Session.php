<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\entity\factory\ISessionEntityFactoryAware;
use umi\session\entity\factory\TSessionEntityFactoryAware;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\exception\OutOfBoundsException;
use umi\session\exception\RuntimeException;

/**
 * Сервис для работы с сессией.
 */
class Session implements ISession, ISessionEntityFactoryAware, ILocalizable
{

    use TLocalizable;
    use TSessionEntityFactoryAware;

    /**
     * @var ISessionNamespace[] $namespaces зарегистрированные пространства имен сессии
     */
    protected $namespaces = [];

    /**
     * {@inheritdoc}
     */
    public function registerNamespace($name, array $validators = [])
    {
        if (isset($this->namespaces[$name])) {
            throw new RuntimeException($this->translate(
                'Namespace "{namespace}" already registered.',
                ['namespace' => $name]
            ));
        }

        $namespace = $this->createSessionNamespace($name, $validators);

        foreach ($validators as $type => $options) {
            $validator = $this->createSessionValidator($type, $options);

            if (!$validator->validate($namespace)) {
                $namespace->clear();
            }
        }

        $this->namespaces[$name] = $namespace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNamespace($name)
    {
        return isset($this->namespaces[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace($name)
    {
        if (!$this->hasNamespace($name)) {
            throw new OutOfBoundsException($this->translate(
                'Session "{name}" has not registered.',
                ['name' => $name]
            ));
        }

        return $this->namespaces[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteNamespace($name)
    {
        if ($this->hasNamespace($name)) {
            $this->getNamespace($name)
                ->clear();

            unset($this->namespaces[$name]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearSession()
    {
        foreach ($this->namespaces as $instance) {
            $instance->clear();
        }

        $this->namespaces = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage($type, array $options = [])
    {
        $storage = $this->createSessionStorage($type);

        /** @noinspection PhpParamsInspection */

        return session_set_save_handler($storage);
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\storage;

use umi\authentication\exception\RuntimeException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\ISession;

/**
 * Класс хранилища в сессии для аутентификации.
 */
class SessionStorage implements IAuthStorage, ILocalizable
{

    use TLocalizable;

    /**
     * Название аттрибута в сессии
     * @internal
     */
    const ATTRIBUTE_NAME = 'identity';
    /**
     * Имя сессии по умолчанию.
     */
    const SESSION_NAME = 'authentication';

    /**
     * @var ISessionNamespace $session сессия
     */
    private $session;

    /**
     * Конструктор.
     * @param array $config конфигурация
     * @param ISession $session
     * @throws RuntimeException если имя пространства имен сессии не указано
     */
    public function __construct(array $config = [], ISession $session)
    {
        $namespace = empty($config['namespace']) ? self::SESSION_NAME : $config['namespace'];

        if (!$session->hasNamespace($namespace)) {
            $session->registerNamespace($namespace);
        }

        $this->session = $session->getNamespace($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($object)
    {
        $this->session->set(self::ATTRIBUTE_NAME, $object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return $this->session->get(self::ATTRIBUTE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function hasIdentity()
    {
        return $this->session->has(self::ATTRIBUTE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        $this->session->del(self::ATTRIBUTE_NAME);

        return $this;
    }
}
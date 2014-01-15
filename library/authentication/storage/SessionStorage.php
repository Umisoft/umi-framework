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
use umi\session\entity\ns\ISessionNamespace;
use umi\session\ISessionAware;
use umi\session\TSessionAware;

/**
 * Класс хранилища характеристик субъекта аутентификации в сессии.
 */
class SessionStorage implements IAuthStorage, ISessionAware
{

    use TSessionAware;

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
     * @var array $options опции хранилища
     */
    protected $options = [];

    /**
     * @var ISessionNamespace $sessionNamespace
     */
    private $sessionNamespace;

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($identity)
    {
        $this->getAuthSessionNamespace()->set(self::ATTRIBUTE_NAME, $identity);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        $identity = $this->getAuthSessionNamespace()->get(self::ATTRIBUTE_NAME);

        if (is_null($identity)) {
            throw new RuntimeException(
                'Authentication identity does not exist.'
            );
        }

        return $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIdentity()
    {
        return $this->getAuthSessionNamespace()->has(self::ATTRIBUTE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        $this->getAuthSessionNamespace()->del(self::ATTRIBUTE_NAME);

        return $this;
    }

    protected function getAuthSessionNamespace()
    {
        if (!$this->sessionNamespace) {
            $namespaceName = empty($this->options['namespace']) ? self::SESSION_NAME : $this->options['namespace'];
            $this->sessionNamespace = $this->getSessionNamespace($namespaceName, true);
        }

        return $this->sessionNamespace;
    }
}
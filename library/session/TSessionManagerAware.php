<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\session\exception\RequiredDependencyException;

/**
 * Трейт для внедрения функциональности работы с менеджером сессии.
 */
trait TSessionManagerAware
{
    /**
     * @var ISessionManager $_sessionService
     */
    private $_sessionManager;

    /**
     * Устанавливает менеджер сессии.
     * @param ISessionManager $sessionManager менеджер
     */
    public final function setSessionManager(ISessionManager $sessionManager)
    {
        $this->_sessionManager = $sessionManager;
    }

    /**
     * Возвращает статус сессии.
     * @return int
     */
    protected final function getSessionStatus()
    {
        return $this->getSessionManager()
            ->getStatus();
    }

    /**
     * Стартует сессию.
     * @return bool
     */
    protected final function startSession()
    {
        return $this->getSessionManager()
            ->start();
    }

    /**
     * Уничтожает сессию.
     * @return bool
     */
    protected final function destroySession()
    {
        return $this->getSessionManager()
            ->destroy();
    }

    /**
     * Записывает данные сессии.
     * @return bool
     */
    protected final function writeSession()
    {
        return $this->getSessionManager()
            ->write();
    }

    /**
     * Перегенерирует идентификатор сессии.
     * @param bool $delOldSession удалять ли файл старой сессии
     * @return bool
     */
    protected final function regenerateSession($delOldSession = false)
    {
        return $this->getSessionManager()
            ->regenerate($delOldSession);
    }

    /**
     * Возвращает идентификатор сессии.
     * @return string
     */
    protected final function getSessionId()
    {
        return $this->getSessionManager()
            ->getId();
    }

    /**
     * Устанавливает идентификатор сессии.
     * @param string $sessionId идентификатор
     * @return $this
     */
    protected final function setSessionId($sessionId)
    {
        $this->getSessionManager()
            ->setId($sessionId);

        return $this;
    }

    /**
     * Возвращает имя сессии.
     * @return string
     */
    protected final function getSessionName()
    {
        return $this->getSessionManager()
            ->getName();
    }

    /**
     * Устанавливает имя сессии.
     * @param string $name имя сессии
     * @return $this
     */
    protected final function setSessionName($name)
    {
        $this->getSessionManager()
            ->setName($name);

        return $this;
    }

    /**
     * Возвращает сервис сессии.
     * @return ISessionManager
     * @throws RequiredDependencyException если сервис не был внедрен.
     */
    private final function getSessionManager()
    {
        if (!$this->_sessionManager) {
            throw new RequiredDependencyException(sprintf(
                'Session manager is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_sessionManager;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\session\entity\ns\ISessionNamespace;
use umi\session\exception\OutOfBoundsException;
use umi\session\exception\RequiredDependencyException;
use umi\session\exception\RuntimeException;

/**
 * Трейт для внедрения функциональности работы с сессией.
 */
trait TSessionAware
{
    /**
     * @var ISession $_sessionService
     */
    private $_sessionService;

    /**
     * @param ISession $sessionService сервис сесии
     */
    public final function setSessionService(ISession $sessionService)
    {
        $this->_sessionService = $sessionService;
    }

    /**
     * Регистрирует пространство имен сессии.
     * @param string $name имя
     * @param array $validators валидаторы
     * @throws RuntimeException если такое пространство имен уже было зарегистрировано
     * @return self
     */
    protected final function registerSessionNamespace($name, array $validators = [])
    {
        $this->getSessionService()
            ->registerNamespace($name, $validators);

        return $this;
    }

    /**
     * Проверяет существование пространства имен.
     * @param string $name имя
     * @return bool
     */
    protected final function hasSessionNamespace($name)
    {
        return $this->getSessionService()
            ->hasNamespace($name);
    }

    /**
     * Возвращает экземпляр ранее зарегистрированного пространства имен.
     * @param string $name имя
     * @throws OutOfBoundsException если пространство имен с таким именем не зарегистрировано
     * @return ISessionNamespace
     */
    protected final function getSessionNamespace($name)
    {
        return $this->getSessionService()
            ->getNamespace($name);
    }

    /**
     * Удаляет пространство имен сесии.
     * @param string $name
     * @return ISessionNamespace
     */
    protected final function removeSessionNamespace($name)
    {
        $this->getSessionService()
            ->deleteNamespace($name);

        return $this;
    }

    /**
     * Чистит сессию.
     * @return self
     */
    protected final function clearSession()
    {
        $this->getSessionService()
            ->clearSession();

        return $this;
    }

    /**
     * Устанавливает хранилище для сессии.
     * @param string $type тип хранилища
     * @param array $options опции
     * @return bool
     */
    protected final function setSessionStorage($type, array $options = [])
    {
        $this->getSessionService()
            ->setStorage($type, $options);

        return $this;
    }

    /**
     * Возвращает сервис сессии.
     * @return ISession
     * @throws RequiredDependencyException если сервис не был внедрен.
     */
    private final function getSessionService()
    {
        if (!$this->_sessionService) {
            throw new RequiredDependencyException(sprintf(
                'Session service is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_sessionService;
    }
}
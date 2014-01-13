<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\event\TEventObservant;
use umi\http\request\IRequest;
use umi\session\exception\RuntimeException;
use umi\session\TSessionAware;

/**
 * Менеджер сессии.
 * Производит открытие, закрытие, уничтожение, ... сессии.
 * Необходим для управления сессией.
 */
class SessionManager implements ISessionManager, ISessionAware
{

    use TSessionAware;

    /**
     * @var int $status
     */
    protected $status;

    /**
     * Конструктор.
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $needSession = (bool) $request->getVar(IRequest::COOKIE, $this->getName(), false);

        switch (true) {
            case session_status() === PHP_SESSION_ACTIVE:
                $this->status = self::STATUS_ACTIVE;
                break;

            case $needSession:
                $this->start();
                break;

            default:
                $this->status = self::STATUS_INACTIVE;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->status === self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_ACTIVE;
        @session_start();

        $sessionId = session_id();
        if (empty($sessionId)) {
            $error = error_get_last();
            throw new RuntimeException(sprintf('Failed to start session: %s.', $error['message']));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_INACTIVE;
        @session_destroy();

        $_SESSION = [];
        $this->clearSession();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write()
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        } else {
            session_write_close();
            $this->status = self::STATUS_CLOSED;

            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($deleteOldSession = false)
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        session_regenerate_id($deleteOldSession);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($sessionId)
    {
        session_id($sessionId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        session_name($name);

        return $this;
    }
}
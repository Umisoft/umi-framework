<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

/**
 * Менеджер сессии.
 */
interface ISessionManager
{
    /**
     * Сессия ни разу не запускалась.
     */
    const STATUS_INACTIVE = 0x01;
    /**
     * Сессия запущена.
     */
    const STATUS_ACTIVE = 0x02;
    /**
     * Сессия была запущена и закрыта.
     */
    const STATUS_CLOSED = 0x03;

    /**
     * Возвращает статус сессии.
     * @return int
     */
    public function getStatus();

    /**
     * Стартует сессию.
     * @return bool
     */
    public function start();

    /**
     * Уничтожает сессию.
     * @return bool
     */
    public function destroy();

    /**
     * Записывает данные сессии.
     * @return bool
     */
    public function write();

    /**
     * Перегенерирует идентификатор сессии.
     * @param bool $delOldSession удалять ли файл старой сессии
     * @return bool
     */
    public function regenerate($delOldSession = false);

    /**
     * Возвращает идентификатор сессии.
     * @return string
     */
    public function getId();

    /**
     * Устанавливает идентификатор сессии.
     * @param string $sessionId идентификатор
     * @return self
     */
    public function setId($sessionId);

    /**
     * Возвращает имя сессии.
     * @return string
     */
    public function getName();

    /**
     * Устанавливает имя сессии.
     * @param string $name имя сессии
     * @return self
     */
    public function setName($name);
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\result;

/**
 * Результат авторизации.
 * Данный результат является результатом работы адаптера по авторизации.
 */
interface IAuthResult
{
    /**
     * Авторизация успешна
     */
    const SUCCESSFUL = 0x0001;
    /**
     * Авторизация не требуется
     */
    const ALREADY = 0x0002;
    /**
     * Неверное имя пользователя
     */
    const WRONG_USERNAME = 0x0101;
    /**
     * Неверный пароль
     */
    const WRONG_PASSWORD = 0x0102;
    /**
     * Нет авторизационных данных
     */
    const WRONG_NO_CREDENTIALS = 0x0103;
    /**
     * Неверные авторизационнные данные (другое)
     */
    const WRONG = 0x0100;

    /**
     * Успешна ли авторизация.
     * @return bool
     */
    public function isSuccessful();

    /**
     * Возвращает статус авторизации.
     * @return int
     */
    public function getStatus();

    /**
     * Ресурс, полученные в результате авторизации.
     * @return mixed|null
     */
    public function getIdentity();
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\response\header;

/**
 * Интерфейс для заголовков HTTP ответа.
 */
interface IHeaderCollection
{
    /**
     * Устанавливает заголовок.
     * @param string $name имя заголовка
     * @param string $value значение
     * @return self
     */
    public function setHeader($name, $value);

    /**
     * Возвращает все заголовки в виде ассоциативного массива.
     * @return array
     */
    public function getHeaders();

    /**
     * Устанавливает значение в cookie.
     * @param string $name имя переменной
     * @param string $value значение
     * @param array $options опции установки cookie
     * @return self
     */
    public function setCookie($name, $value, $options = []);

    /**
     * Возвращает данные cookie в виде ассоциативного массива.
     * @return array
     */
    public function getCookies();

    /**
     * Отправляет HTTP заголовки.
     * @return void
     */
    public function send();
}
 
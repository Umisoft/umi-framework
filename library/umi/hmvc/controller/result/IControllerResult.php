<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\result;

use umi\spl\container\IContainer;

/**
 * Интерфейс для обертки результата работы контроллера.
 */
interface IControllerResult extends IContainer
{
    /**
     * Устанавливает код ответа для результата.
     * @param int $code код ответа
     * @return self
     */
    public function setCode($code);

    /**
     * Возвращает установленный код ответа результата.
     * @return string код ответа
     */
    public function getCode();

    /**
     * Устанавливает переменные результата.
     * @param array $variables переменные
     * @return self
     */
    public function setVariables(array $variables);

    /**
     * Возвращает все переменные результата.
     * @return array переменные
     */
    public function getVariables();

    /**
     * Устанавливает HTTP заголовок в ответ.
     * @param string $name имя заголовка
     * @param mixed $value значение
     * @return self
     */
    public function setHeader($name, $value);

    /**
     * Возвращает HTTP заголовки.
     * @return array
     */
    public function getHeaders();

    /**
     * Устанавливает cookie в ответ.
     * @param string $name имя
     * @param string $value значение
     * @param array $options опции
     * @return self
     */
    public function setCookie($name, $value, $options = []);

    /**
     * Возвращает cookie.
     * @return array
     */
    public function getCookies();

    /**
     * Возвращает шаблон для результата.
     * @return string
     */
    public function getTemplate();
}
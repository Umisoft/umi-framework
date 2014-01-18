<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\response\model;

/**
 * Интерфейс для обертки результата работы контроллера.
 */
interface IDisplayModel
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
     * Возвращает имя шаблона для результата.
     * @return string
     */
    public function getTemplateName();
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\result;

/**
 * Интерфейс фабрики для создания результатов работы контроллера.
 */
interface IControllerResultFactory
{
    /**
     * Создает результат работы контроллера.
     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return IControllerResult
     */
    public function createControllerResult($template, array $variables = []);
}
 
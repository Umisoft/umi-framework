<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine;

/**
 * Интерфейс шаблонизатора.
 */
interface ITemplateEngine
{

    /**
     * Устанавливает опции шаблонизатора.
     * @param array $options опции
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Отображает заданный шаблон используя переменные.
     * @param string $templateName имя шаблона
     * @param array $variables переменные
     * @return string отображение
     */
    public function render($templateName, array $variables = []);
}

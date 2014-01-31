<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view;

/**
 * Интерфейс для рендеринга шаблона.
 */
interface IViewRenderer
{
    /**
     * Тип шаблонизатора для рендеринга.
     */
    const OPTION_TYPE = 'type';

    /**
     * Производит рендеринг шаблона.
     * @param string $templateName имя шаблона
     * @param array $params параметры для шаблонизации.
     * @return string результат рендеринга
     */
    public function render($templateName, array $params = []);
}
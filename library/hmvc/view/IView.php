<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view;

use umi\hmvc\component\response\IComponentResponse;

/**
 * Интерфейс отображения.
 */
interface IView
{
    /** Тип шаблонизатора для рендеринга. */
    const OPTION_TYPE = 'type';

    /**
     * Производит рендеринг результата работы контроллера. Возвращает результат
     * отображения в виде результата работы компонента.
     * @param string $template
     * @param array $params
     * @return IComponentResponse результат работы компонента
     */
    public function render($template, array $params = []);
}
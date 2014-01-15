<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\style;

/**
 * Помощник шаблонов для отображения стилей.
 */
class StyleHelper
{

    /**
     * Возвращает коллекцию стилей.
     * @return StyleCollection
     */
    public function __invoke()
    {
        return new StyleCollection();
    }
}
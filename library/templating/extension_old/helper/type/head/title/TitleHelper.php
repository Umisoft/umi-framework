<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\title;

/**
 * Помощник шаблонов для отображения заголовка страницы.
 */
class TitleHelper
{
    /**
     * Возвращает коллекцию информации о заголовке страницы.
     * @return TitleCollection
     */
    public function __invoke()
    {
        return new TitleCollection();
    }
}
 
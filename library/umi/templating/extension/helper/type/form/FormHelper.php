<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\form;

/**
 * Помошник вида для вывода форм.
 */
class FormHelper
{

    /**
     * Возвращает коллекцию помошников вида.
     * @return FormHelperCollection
     */
    public function __invoke()
    {
        return new FormHelperCollection();
    }
}
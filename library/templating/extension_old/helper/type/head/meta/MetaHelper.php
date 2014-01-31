<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\meta;

/**
 * Помощник шаблонов для отображения мета информации.
 */
class MetaHelper
{
    /**
     * Возвращает коллекцию мета информации.
     * @return MetaCollection
     */
    public function __invoke()
    {
        return new MetaCollection();
    }
}
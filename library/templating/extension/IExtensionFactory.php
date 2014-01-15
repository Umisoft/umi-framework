<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension;

use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * Интерфейс расширения для шаблонизатора.
 */
interface IExtensionFactory
{
    /**
     * Создает коллекцию помощников для шаблонов.
     * @return IHelperCollection
     */
    public function createHelperCollection();

    /**
     * Создает коллекцию помощников шаблонов по умолчанию.
     * @return IHelperCollection
     */
    public function getDefaultHelperCollection();
}
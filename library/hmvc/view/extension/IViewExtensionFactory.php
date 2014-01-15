<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\extension;

use umi\templating\extension\helper\collection\IHelperCollection;
use umi\templating\extension\IExtensionFactory;

/**
 * Фабрика расширений для отображения.
 */
interface IViewExtensionFactory extends IExtensionFactory
{
    /**
     * Создает коллекцию помощников вида.
     * @return IHelperCollection
     */
    public function createViewHelperCollection();

    /**
     * Возвращает помощники вида по умолчанию.
     *
     * Помощники вида по умолчанию указываются в настройках
     * HMVC и доступны для всех компонентов.
     *
     * @return IHelperCollection
     */
    public function getDefaultViewHelperCollection();
}
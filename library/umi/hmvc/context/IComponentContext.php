<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

use umi\hmvc\component\IComponent;

/**
 * Интерфейс для поддержки внедрения компонента из контекста.
 */
interface IComponentContext
{
    /**
     * Устанавливает контекстно-зависимый компонент.
     * @param IComponent $component компонент
     */
    public function setContextComponent(IComponent $component = null);
}
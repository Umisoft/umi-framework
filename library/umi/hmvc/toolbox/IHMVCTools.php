<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox;

use umi\hmvc\component\IComponentFactory;
use umi\hmvc\component\request\IComponentRequestFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Интерфейс инструментов для Model-TemplateView-Controller архитектуры.
 * @internal используется только внутри MVC компонента
 */
interface IHMVCTools extends IToolbox
{
    /**
     * Короткий alias для доступа.
     */
    const ALIAS = 'hmvc';

    /**
     * Возвращает фабрику для создания MVC компонентов.
     * @return IComponentFactory
     */
    public function getComponentFactory();

    /**
     * Возвращает фабрику HTTP запросов компонента.
     * @return IComponentRequestFactory
     */
    public function getComponentRequestFactory();
}
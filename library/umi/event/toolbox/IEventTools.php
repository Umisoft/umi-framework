<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event\toolbox;

use umi\event\IEventManager;
use umi\toolkit\toolbox\IToolbox;

/**
 * Инструменты для поддержки событий.
 */
interface IEventTools extends IToolbox
{
    /**
     * Создает и возвращает новый менеджер событий
     * @return IEventManager
     */
    public function createEventManager();
}

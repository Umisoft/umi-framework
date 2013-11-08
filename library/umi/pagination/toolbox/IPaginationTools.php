<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\toolbox;

use umi\pagination\IPaginatorFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Интерфейс инструментов пагинатора.
 */
interface IPaginationTools extends IToolbox
{
    /**
     * Короткий alias.
     */
    const ALIAS = 'pagination';

    /**
     * Возвращает фабрику пагинаторов.
     * @return IPaginatorFactory
     */
    public function getPaginatorFactory();
}
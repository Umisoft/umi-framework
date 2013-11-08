<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\toolbox;

use umi\form\IEntityFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов для работы с формами.
 */
interface IFormTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'form';

    /**
     * Возвращает фабрику элементов формы.
     * @return IEntityFactory
     */
    public function getEntityFactory();
}
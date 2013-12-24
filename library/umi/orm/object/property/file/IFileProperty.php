<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property\file;

use umi\orm\object\property\IProperty;

/**
 * Интерфейс для свойства со значением типа файл.
 */
interface IFileProperty extends IProperty
{
    /**
     * Помечает свойство как модифицированное и обновляет внутреннюю информацию.
     * @internal
     * @return self
     */
    public function update();
}
 
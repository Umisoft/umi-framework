<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\orm\object\IObject;

/**
 * Интрфейс поля с вычисляемым значением.
 */
interface ICalculableField extends IField
{

    /**
     * Вычисляет и возвращает значение для записи в БД.
     * @param IObject $object объект, для которого вычисляется значение
     * @return string|int|float
     */
    public function calculateDBValue(IObject $object);

}

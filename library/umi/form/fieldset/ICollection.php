<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\fieldset;

use umi\form\IFormEntity;

/**
 * Интерфейс коллекции.
 * Коллекция - это группа полей, содержащая один элемент.
 * Размер коллекции автоматически изменяется,
 * в зависимости от установленных в нее данных.
 */
interface ICollection extends IFieldset
{
    /**
     * Возвращает пустой элемент коллекции.
     * @return IFormEntity
     */
    public function getEmptyEntity();
}
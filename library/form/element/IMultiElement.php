<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

/**
 * Интерфейс элемента с определенными значениями.
 */
interface IMultiElement extends IElement
{
    /**
     * Возвращает список возможных значений элемента.
     * @return \ArrayObject
     */
    public function getChoices();
}
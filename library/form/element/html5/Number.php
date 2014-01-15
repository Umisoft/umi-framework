<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element\html5;

use umi\form\element\Text;

/**
 * HTML5 элемент формы - Число (number).
 * @example <input type="number" />
 */
class Number extends Text
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'number';

    /**
     * @var array $attributes аттрибуты
     */
    protected $attributes = [
        'type' => self::TYPE_NAME
    ];
}
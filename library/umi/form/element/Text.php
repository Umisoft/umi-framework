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
 * Элемент формы - Текстовое поле(text).
 * @example <input type="text" />
 */
class Text extends BaseElement
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'text';

    /**
     * @var array $attributes аттрибуты
     */
    protected $attributes = [
        'type' => 'text'
    ];
}
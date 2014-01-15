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
 * Элемент формы - флаг(checkbox).
 * @example <input type="checkbox" />
 */
class Checkbox extends BaseElement
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'checkbox';

    /**
     * @var array $attributes аттрибуты
     */
    protected $attributes = [
        'type'  => 'checkbox',
        'value' => 1
    ];

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return parent::getValue() ? $this->attributes['value'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value, $isRaw = false)
    {
        if ((bool) $value) {
            $this->attributes['checked'] = 'checked';
        } elseif (isset($this->attributes['checked'])) {
            unset($this->attributes['checked']);
        }

        return parent::setValue((bool) $value, $isRaw);
    }
}
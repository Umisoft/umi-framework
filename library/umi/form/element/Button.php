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
 * Элемент формы - кнопка(button).
 * @example <button>example button</button>
 */
class Button extends BaseElement implements IButton
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'button';

    /**
     * @var \ArrayObject $options опции элемента
     */
    protected $options = [
        self::OPTION_EXCLUDE => true
    ];

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getLabel();
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

use umi\form\exception\InvalidArgumentException;

/**
 * Базовый класс для элементов с несколькими значениями.
 */
abstract class BaseMultiElement extends BaseElement implements IMultiElement
{
    /**
     * @var \ArrayObject $choices варианты значений элемента
     */
    protected $choices;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, array $attributes = [], array $options = [])
    {
        parent::__construct($name, $attributes, $options);

        $choices = [];
        if (isset($this->options['choices'])) {
            $choices = $this->options['choices'];
        }

        $this->choices = new \ArrayObject($choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException если значение не в списке.
     */
    public function setValue($value, $isRaw = false)
    {
        if (!$isRaw) {
            $value = $this->filter($value);
        }

        if (!isset($this->getChoices()[$value])) {
            throw new InvalidArgumentException($this->translate(
                'Value "{value}" is not in available values list.',
                ['value' => $value]
            ));
        }

        return parent::setValue($value, true);
    }
}
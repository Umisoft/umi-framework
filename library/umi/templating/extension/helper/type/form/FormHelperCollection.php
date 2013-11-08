<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\form;

use umi\form\element\Button;
use umi\form\element\IElement;
use umi\form\element\IMultiElement;
use umi\form\element\Select;
use umi\form\element\Submit;
use umi\form\element\Textarea;
use umi\form\IForm;
use umi\templating\exception\InvalidArgumentException;

/**
 * Помошник вида для генерации textarea элемента формы.
 */
class FormHelperCollection
{

    /**
     * Генерирует открывающий тег формы для объекта $form.
     * @param IForm $form объект формы
     * @return string сгенерированный тэг
     */
    public function openTag(IForm $form)
    {
        $attributes = $this->buildAttributes($form->getAttributes());

        return '<form ' . $attributes . '>';
    }

    /**
     * Генерирует закрывающий тег формы.
     * @return string сгенерированный тэг
     */
    public function closeTag()
    {
        return '</form>';
    }

    /**
     * Генерирует элемент формы. Выбирает нужный помошник вида
     * в зависимости от типа элемента.
     * @param IElement $element элемент формы
     * @throws InvalidArgumentException если элемент не может быть выведен.
     * @return string сгенерированный тэг
     */
    public function formElement(IElement $element)
    {
        if ($element instanceof Textarea) {
            return $this->formTextarea($element);
        } elseif ($element instanceof Select) {
            return $this->formSelect($element);
        } elseif ($element instanceof Submit) {
            return $this->formInput($element);
        } elseif ($element instanceof Button) {
            // todo: is it right?
            throw new InvalidArgumentException("Button element should be rendered without Helper.");
        } else {
            return $this->formInput($element);
        }
    }

    /**
     * Генерирует элемент формы. Выбирает нужный помошник вида
     * в зависимости от типа элемента.
     * @param IMultiElement $element элемент формы
     * @return string сгенерированный тэг
     */
    public function formSelect(IMultiElement $element)
    {
        $attributes = $this->buildAttributes($element->getAttributes());
        $html = '<select ' . $attributes . ' value="' . $element->getValue() . '">';

        foreach ($element->getChoices() as $value => $label) {
            $attr = ['value' => $value];

            if ($value == $element->getValue()) {
                $attr += [
                    'selected' => 'selected'
                ];
            }

            $html .= '<option ' . $this->buildAttributes(new \ArrayObject($attr)) . '>' . $label . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Генерирует <textarea> элемент формы.
     * @param IElement $element элемент формы
     * @return string сгенерированный тэг
     */
    public function formTextarea(IElement $element)
    {
        $attributes = $this->buildAttributes($element->getAttributes());

        return '<textarea ' . $attributes . '>' . $element->getValue() . '</textarea>';
    }

    /**
     * Генерирует <input> элемент формы.
     * @param IElement $element элемент формы
     * @return string сгенерированный тэг
     */
    public function formInput(IElement $element)
    {
        return $this->buildInput(
            $element->getAttributes(),
            $element->getValue()
        );
    }

    /**
     * Генерирует строку аттрибутов для элемента.
     * @param \ArrayObject $attributes массив аттрибутов элемента
     * @return string
     */
    protected function buildAttributes(\ArrayObject $attributes)
    {
        $strings = [];

        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $strings[] = $key . '="' . $value . '"';
        }

        return implode(' ', $strings);
    }

    /**
     * Создает <input> элемент формы по аттрибутам и значению.
     * @param \ArrayObject $attributes аттрибуты
     * @param mixed $value значение
     * @return string элемент формы
     */
    private function buildInput(\ArrayObject $attributes, $value)
    {
        return '<input ' . $this->buildAttributes($attributes) . ' value="' . $value . '" />';
    }
}

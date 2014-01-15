<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\fieldset;

use umi\form\element\IElement;
use umi\form\exception\RuntimeException;
use umi\form\IForm;
use umi\form\IFormEntity;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Коллекция элементов формы.
 */
class Collection extends Fieldset implements ICollection, \Iterator, ILocalizable
{

    use TLocalizable;

    /**
     * Тип элемента формы.
     */
    const TYPE_NAME = 'collection';

    /**
     * @var IFormEntity $element элемент формы
     */
    protected $element;

    /**
     * Конструктор.
     * @param string $name имя коллекции
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @param array $elements элементы
     * @throws RuntimeException если неверное кол-во элементов передано
     */
    public function __construct($name, array $attributes, array $options = [], array $elements = [])
    {
        parent::__construct($name, $attributes, $options, $elements);

        if (count($this->elements) != 1) {
            throw new RuntimeException($this->translate(
                'Form element "{name}" should contain definition of one element.',
                ['name' => $name]
            ));
        }

        $this->element = array_values($this->elements)[0];
        $this->elements = [];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $elements = $this->elements;
        $this->elements = [];

        $data = isset($data[$this->getName()]) ? $data[$this->getName()] : [];

        foreach ($data as $key => $val) {
            if (!isset($elements[$key])) {
                $element = clone $this->getEmptyEntity();

                $element->getAttributes()['name'] = $this->getName() . "[$key]";

                if ($element instanceof IForm) {
                    $element->setIsSubForm(true);
                }
            } else {
                $element = $elements[$key];
                unset($elements[$key]);
            }

            if ($element instanceof IElement) {
                $element->setValue($val);
            } elseif ($element instanceof IForm) {
                if (is_object($val)) {
                    $element->bindObject($val);
                } else {
                    $element->setData($val);
                }
            }

            $this->elements[$key] = $element;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            $this->getName() => parent::getData()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyEntity()
    {
        return $this->element;
    }
}
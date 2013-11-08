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
use umi\form\exception\OutOfBoundsException;
use umi\form\exception\RuntimeException;
use umi\form\FormEntity;
use umi\form\IForm;
use umi\form\IFormEntity;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс простой группы полей.
 */
class Fieldset extends FormEntity implements \Iterator, IFieldset, ILocalizable
{

    use TLocalizable;

    /**
     * Тип элемента формы.
     */
    const TYPE_NAME = 'fieldset';

    /**
     * @var IFormEntity[] $elements элементы группы полей
     */
    protected $elements = [];

    /**
     * Конструктор.
     * @param string $name имя формы
     * @param array $attributes
     * @param array $options опции
     * @param IFormEntity[] $elements
     * @throws \umi\form\exception\RuntimeException
     */
    public function __construct($name, array $attributes = [], array $options = [], array $elements = [])
    {
        parent::__construct($name, $attributes, $options);

        $this->elements = $elements;

        foreach ($this->elements as $name => $element) {
            if (!$element instanceof IFormEntity) {
                throw new RuntimeException($this->translate(
                        'Element "{name}" has not implemented IFormEntity.',
                        ['name' => $name]
                    )
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getElement($name)
    {
        if (!isset($this->elements[$name])) {
            throw new OutOfBoundsException($this->translate(
                'Element "{name}" not found in fieldset.',
                ['name' => $name]
            ));
        }

        return $this->elements[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        foreach ($this->elements as $entity) {
            if ($entity instanceof IElement) {
                $key = $entity->getName();
                if (isset($data[$key])) {
                    $entity->setValue($data[$key]);
                }
            } elseif ($entity instanceof IForm) {
                // todo: is it right?
                if (isset($data[$entity->getName()])) {
                    $val = $data[$entity->getName()];
                    if (is_object($val)) {
                        $entity->bindObject($val);
                    } else {
                        $entity->setData($val);
                    }
                }
            } elseif ($entity instanceof IFieldset) {
                $entity->setData($data);
            } else {
                throw new RuntimeException($this->translate(
                        'Element "{name}" has not implemented IElement or IFieldset.',
                        ['name' => $entity->getName()]
                    )
                );
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $cleanData = [];

        foreach ($this->elements as $key => $entity) {
            if (isset($entity->getOptions()[self::OPTION_EXCLUDE]) && $entity->getOptions()[self::OPTION_EXCLUDE]) {
                continue;
            }

            if ($entity instanceof IElement) {
                $cleanData[$key] = $entity->isValid() ? $entity->getValue() : null;
            } elseif ($entity instanceof IForm) {
                $cleanData[$key] = $entity->getData();
            } elseif ($entity instanceof IFieldset) {
                $cleanData += $entity->getData();
            } else {
                throw new RuntimeException($this->translate(
                        'Element "{name}" has not implemented IElement or IFieldset.',
                        ['name' => $entity->getName()]
                    )
                );
            }
        }

        return $cleanData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        $messages = [];

        foreach ($this->elements as $element) {
            if ($element->getMessages()) {
                $messages[$element->getName()] = $element->getMessages();
            }
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $isValid = true;

        foreach ($this->elements as $element) {
            $isValid = $isValid && $element->isValid();
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return key($this->elements) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->elements);
    }

    /**
     * Выполняет клонирование элементов группы при клонировании группы полей.
     */
    public function __clone()
    {
        parent::__clone();

        foreach ($this->elements as $key => $element) {
            $this->elements[$key] = clone $element;
        }
    }
}
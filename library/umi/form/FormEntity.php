<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

use umi\form\exception\InvalidArgumentException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс элемента формы.
 */
abstract class FormEntity implements IFormEntity, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $name имя элемента формы
     */
    protected $name;
    /**
     * @var \ArrayObject $attributes аттрибуты элемента
     */
    protected $attributes = [];
    /**
     * @var \ArrayObject $options опции элемента
     */
    protected $options = [];

    /**
     * @var string $label заголовок для элемента
     */
    private $label;

    /**
     * Конструктор.
     * @param string $name имя элемента
     * @param array $attributes аттрибуты
     * @param array $options опции
     * @throws InvalidArgumentException если имя элемента не указано
     */
    public function __construct($name, array $attributes = [], array $options = [])
    {
        if (is_null($name)) {
            throw new InvalidArgumentException($this->translate(
                'Cannot create form element without name.'
            ));
        }

        $this->name = $name;
        $this->attributes = new \ArrayObject(['name' => $name] + $attributes + $this->attributes);
        $this->options = new \ArrayObject($options + $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Magic method клонирования объекта.
     */
    public function __clone()
    {
        if ($this->attributes instanceof \ArrayObject) {
            $this->attributes = new \ArrayObject($this->attributes->getArrayCopy());
        }

        if ($this->options instanceof \ArrayObject) {
            $this->options = new \ArrayObject($this->options->getArrayCopy());
        }
    }
}
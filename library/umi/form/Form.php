<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

use umi\event\IEvent;
use umi\event\TEventObservant;
use umi\form\binding\IDataBinding;
use umi\form\fieldset\Fieldset;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс форм.
 */
class Form extends Fieldset implements IForm, \Iterator, ILocalizable
{

    use TEventObservant;

    /**
     * Тип элемента формы.
     */
    const TYPE_NAME = 'form';

    /**
     * @var IDataBinding $bindObject биндинг объект
     */
    protected $bindObject = null;

    /**
     * Конструктор.
     * @param string $name имя формы
     * @param array $attributes
     * @param array $options опции
     * @param array $elements
     */
    public function __construct($name, $attributes = [], array $options = [], array $elements = [])
    {
        parent::__construct($name, $attributes + ['method' => 'get'], $options, $elements);

        foreach ($this->elements as $element) {
            if ($element instanceof IForm) {
                $element->setIsSubForm(true);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return isset($this->attributes['method']) ? $this->attributes['method'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSubForm($isSubform)
    {
        foreach ($this->elements as $element) {
            $name = $element->getName();
            $element->getAttributes()['name'] = $isSubform ? $this->getSubformElementName($name) : $name;

            if ($element instanceof IForm) {
                $element->setIsSubForm($isSubform);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        parent::setData($data);

        if ($this->bindObject && $this->isValid()) {
            $this->bindObject->setData(parent::getData());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if ($this->bindObject) {
            return $this->bindObject;
        }

        return parent::getData();
    }

    /**
     * {@inheritdoc}
     */
    public function bindObject(IDataBinding $object)
    {
        $this->setData($object->getData());

        $this->bindObject = $object;
        $this->subscribeTo($this->bindObject);

        return $this;
    }

    /**
     * Возвращает заданное имя, как имя элемента дочерней формы.
     * @param string $name имя элемента
     * @return string
     */
    protected function getSubformElementName($name)
    {
        $formName = isset($this->attributes['name']) ? $this->attributes['name'] : $this->getName();

        return $formName . '[' . $name . ']';
    }

    /**
     * {@inheritdoc}
     */
    protected function bindLocalEvents() {
        $this->bindEvent(
            IDataBinding::EVENT_UPDATE,
            function (IEvent $e) {
                $this->setData($e->getParams() ? : $this->bindObject->getData());
            }
        );
    }
}
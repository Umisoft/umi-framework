<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

use umi\filter\IFilter;
use umi\filter\IFilterAware;
use umi\filter\IFilterCollection;
use umi\filter\TFilterAware;
use umi\form\FormEntity;
use umi\validation\IValidationAware;
use umi\validation\IValidatorCollection;
use umi\validation\TValidationAware;

/**
 * Абстрактный базовый класс элемента формы.
 */
abstract class BaseElement extends FormEntity implements IElement, IValidationAware, IFilterAware
{

    use TValidationAware;
    use TFilterAware;

    /**
     * @var bool $isValid флаг валидности значения в элементе
     */
    protected $isValid = true;
    /**
     * @var IFilter $filters фильтры элемента
     */
    protected $filters;
    /**
     * @var IValidatorCollection $validators валидаторы элемента
     */
    protected $validators;
    /**
     * @var array $messages сообщения валидации
     */
    protected $messages = [];
    /**
     * @var string $value значение
     */
    private $value;

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value, $isRaw = false)
    {
        if (!$isRaw) {
            $value = $this->filter($value);
        }

        $this->value = $value;
        $this->isValid = $this->validate($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(IFilterCollection $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Возвращает установленные валидаторы для формы.
     * @return IFilter
     */
    public function getFilters()
    {
        if (!$this->filters) {
            $this->filters = $this->createFilterCollection();
        }

        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidators(IValidatorCollection $validators)
    {
        $this->validators = $validators;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidators()
    {
        if (!$this->validators) {
            $this->validators = $this->createValidatorCollection();
        }

        return $this->validators;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Возвращает отфильтрованное значение.
     * @param string $value
     * @return mixed
     */
    protected function filter($value)
    {
        if (!$this->filters instanceof IFilterCollection) {
            return $value;
        }

        return $this->filters->filter($value);
    }

    /**
     * Проверяет значение на сооветсвие валидаторам.
     * @param mixed $value значение
     * @return bool
     */
    protected function validate($value)
    {
        if (!$this->validators instanceof IValidatorCollection) {
            return true;
        }

        $isValid = $this->validators->isValid($value);
        $this->messages = $this->validators->getMessages();

        return $isValid;
    }
}
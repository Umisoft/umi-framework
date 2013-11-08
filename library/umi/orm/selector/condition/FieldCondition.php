<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\selector\condition;

use umi\dbal\builder\ISelectBuilder;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\object\IObject;
use umi\orm\selector\ISelector;

/**
 * Простое выражение селектора для поля.
 */
class FieldCondition implements IFieldCondition
{
    /**
     * @var ISelector $selector селектор, к которому относится выражение
     */
    protected $selector;
    /**
     * @var IField $field поле типа данных
     */
    protected $field;
    /**
     * @var string $fieldColumn источник данных поля
     */
    protected $fieldColumn;
    /**
     * @var string $operator оператор выражения
     */
    protected $operator = self::OPERATOR_EQUALS;
    /**
     * @var mixed $expression условие выражения
     */
    protected $expression;
    /**
     * @var string $placeholder плейсхолдер
     */
    protected $placeholder;

    /**
     * Конструктор.
     * @param ISelector $selector селектор, к которому относится выражение
     * @param IField $field поле типа данных
     * @param string $collectionAlias алиас для коллекции
     * @param string $placeholder уникальный плейсхолдер для поля
     * @param string $localeId идентификатор локали (для локализованных полей)
     * @return self
     */
    public function __construct(ISelector $selector, IField $field, $collectionAlias, $placeholder, $localeId = null)
    {
        $columnName = $field instanceof ILocalizableField && $field->getIsLocalized() ? $field->getColumnName(
            $localeId
        ) : $field->getColumnName();
        $this->selector = $selector;
        $this->field = $field;
        $this->fieldColumn = $collectionAlias . ISelector::FIELD_SEPARATOR . $columnName;
        $this->placeholder = $placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function equals($value)
    {
        $this->operator = self::OPERATOR_EQUALS;
        $this->expression = $this->prepareValue($value);

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function in(array $value)
    {
        $this->operator = self::OPERATOR_IN;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($value)
    {
        $this->operator = self::OPERATOR_NOTEQUALS;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function more($value)
    {
        $this->operator = self::OPERATOR_MORE;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function equalsOrMore($value)
    {
        $this->operator = self::OPERATOR_EQMORE;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function less($value)
    {
        $this->operator = self::OPERATOR_LESS;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function equalsOrLess($value)
    {
        $this->operator = self::OPERATOR_EQLESS;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function between($minValue, $maxValue)
    {
        $this->operator = self::OPERATOR_BETWEEN;
        $this->expression = array($minValue, $maxValue);

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull()
    {
        $this->operator = self::OPERATOR_ISNULL;
        $this->expression = null;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function notNull()
    {
        $this->operator = self::OPERATOR_NOTNULL;
        $this->expression = null;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function like($value)
    {
        $this->operator = self::OPERATOR_LIKE;
        $this->expression = $value;

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ISelectBuilder $selectBuilder)
    {
        switch ($this->operator) {
            case IFieldCondition::OPERATOR_BETWEEN:
                return $this->applyBetweenCondition($selectBuilder);
            case IFieldCondition::OPERATOR_IN:
                return $this->applyInCondition($selectBuilder);
            default:
                return $this->applySimpleCondition($selectBuilder);
        }
    }

    /**
     * Подготавливает значение
     * @param mixed $value
     * @return mixed
     */
    protected function prepareValue($value)
    {
        if ($value instanceof IObject) {
            return $value->getId();
        }

        return $value;
    }

    /**
     * Прнименяет простое условие на выборку
     * @param ISelectBuilder $selectBuilder
     * @return $this
     */
    protected function applySimpleCondition(ISelectBuilder $selectBuilder)
    {
        $selectBuilder->expr($this->fieldColumn, $this->operator, $this->placeholder);
        $selectBuilder->bindValue($this->placeholder, $this->expression, $this->field->getDataType());

        return $this;
    }

    /**
     * Прнименяет условие на выборку "IN"
     * @param ISelectBuilder $selectBuilder
     * @return $this
     */
    protected function applyInCondition(ISelectBuilder $selectBuilder)
    {
        $selectBuilder->expr($this->fieldColumn, $this->operator, $this->placeholder);
        $selectBuilder->bindArray($this->placeholder, $this->expression);

        return $this;
    }

    /**
     * Прнименяет условие на выборку "BETWEEN"
     * @param ISelectBuilder $selectBuilder
     * @return $this
     */
    protected function applyBetweenCondition(ISelectBuilder $selectBuilder)
    {
        list($minValue, $maxValue) = $this->expression;

        $minPlaceholder = $this->placeholder . ISelector::PLACEHOLDER_SEPARATOR . 'min';
        $maxPlaceholder = $this->placeholder . ISelector::PLACEHOLDER_SEPARATOR . 'max';

        $selectBuilder->begin()
            ->expr($this->fieldColumn, IFieldCondition::OPERATOR_EQMORE, $minPlaceholder)
            ->expr($this->fieldColumn, IFieldCondition::OPERATOR_EQLESS, $maxPlaceholder)
            ->bindValue($minPlaceholder, $minValue, $this->field->getDataType())
            ->bindValue($maxPlaceholder, $maxValue, $this->field->getDataType())
            ->end();

        return $this;
    }
}

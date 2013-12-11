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
use umi\orm\selector\ISelector;

/**
 * Простое выражение селектора для поля.
 */
interface IFieldCondition
{
    /**
     * Оператор - эквивалентно.
     */
    const OPERATOR_EQUALS = '=';
    /**
     * Оператор - не эквивалентно.
     */
    const OPERATOR_NOTEQUALS = '!=';
    /**
     * Оператор - содержит.
     */
    const OPERATOR_IN = 'IN';
    /**
     * Оператор - эквивалентно(нечеткое сравнение).
     */
    const OPERATOR_LIKE = 'LIKE';
    /**
     * Оператор - больше.
     */
    const OPERATOR_MORE = '>';
    /**
     * Оператор - меньше.
     */
    const OPERATOR_LESS = '<';
    /**
     * Оператор - больше или равно.
     */
    const OPERATOR_EQMORE = '>=';
    /**
     * Оператор - меньше или равно.
     */
    const OPERATOR_EQLESS = '<=';
    /**
     * Оператор - между.
     */
    const OPERATOR_BETWEEN = 'BETWEEN';
    const OPERATOR_ISNULL = 'IS';
    const OPERATOR_NOTNULL = 'IS NOT';

    /**
     * Возвращает поле для условия
     * @return IField
     */
    public function getField();

    /**
     * Возвращает плейсхолдер для условия
     * @internal
     * @return string
     */
    public function getPlaceholder();

    /**
     * Устанавливает поиск по точному значению поля.
     * <code>
     *     $selector->where('login')->equals('guest');
     *     ...
     *     // $group - объект (IObject), представляющий группу пользователя
     *     $selector->where('group')->equals($group);
     *     ...
     *     $selector->where('price')->equals(100.500);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function equals($value);

    /**
     * Устанавливает поиск по массиву заданных значений
     * <code>
     *     $selector->where('id')->in(array(1,2,3));
     * </code>
     * @param array $value
     * @return ISelector
     */
    public function in(array $value);

    /**
     * Устанавливает поиск по значениям, отличным от заданного.
     * <code>
     *     $selector->where('login')->notEquals('guest');
     *     ...
     *     // $group - объект (IObject), представляющий группу пользователя
     *     $selector->where('group')->notEquals($group);
     *     ...
     *     $selector->where('price')->notEquals(100.500);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function notEquals($value);

    /**
     * Устанавливает поиск по значениям больше заданного. Применим только для числовых полей.
     * <code>
     *     $selector->where('price')->more(100);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function more($value);

    /**
     * Устанавливает поиск по значениям равным или больше заданного. Применим только для числовых полей.
     * <code>
     *     $selector->where('price')->equalsOrMore(100);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function equalsOrMore($value);

    /**
     * Устанавливает поиск по значениям меньше заданного. Применим только для числовых полей.
     * <code>
     *     $selector->where('price')->less(100);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function less($value);

    /**
     * Устанавливает поиск по значениям равным или меньше заданного. Применим только для числовых полей.
     * <code>
     *     $selector->where('price')->equalsOrLess(100);
     * </code>
     * @param mixed $value значение свойства
     * @return ISelector
     */
    public function equalsOrLess($value);

    /**
     * Устанавливает поиск по граничным значениям (включительно). Применим только для числовых полей.
     * <code>
     *     $selector->where('price')->between(100, 200.2);
     * </code>
     * @param mixed $minValue минимальное значение свойства
     * @param mixed $maxValue максимальное значение свойства
     * @return ISelector
     */
    public function between($minValue, $maxValue);

    /**
     * Устанавливает поиск по значениям равным NULL.
     * <code>
     *     $selector->where('login')->isNull();
     *     ...
     *     // $group - объект (IObject), представляющий группу пользователя
     *     $selector->where('group')->isNull();
     *     ...
     *     $selector->where('price')->isNull();
     * </code>
     * @return ISelector
     */
    public function isNull();

    /**
     * Устанавливает поиск по значениям неравным NULL.
     * <code>
     *     $selector->where('login')->notNull();
     *     ...
     *     // $group - объект (IObject), представляющий группу пользователя
     *     $selector->where('group')->notNull();
     *     ...
     *     $selector->where('price')->notNull();
     * </code>
     * @return ISelector
     */
    public function notNull();

    /**
     * Устанавливает поиск по подстроке. Применим только для строковых полей.
     * <code>
     *    $selector->where('login')->equals('anon%');
     *    ...
     *    // внимание! в этом случае индекс для поля не будет использован:
     *    $selector->where('login')->equals('%anon%');
     * </code>
     * @param string $value выражение для поиска
     * @return ISelector
     */
    public function like($value);

    /**
     * Возвращает оператор сравнения для условия.
     * @return string
     */
    public function getOperator();

    /**
     * Возвращает выражение для условия.
     * @return string
     */
    public function getExpression();

    /**
     * Применяет условие выборки к билдеру запросов.
     * @internal
     * @param ISelectBuilder $selectBuilder
     * @return self
     */
    public function apply(ISelectBuilder $selectBuilder);
}

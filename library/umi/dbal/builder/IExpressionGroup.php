<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

/**
 * Группа выражений запроса.
 * Группа может состоять из вложенных групп и простых выражений
 * Составные группы и простые выражения складываются по OR, AND, XOR
 * в зависимости от указанного режима группы.
 */
interface IExpressionGroup
{

    const MODE_AND = 'AND';
    const MODE_OR = 'OR';
    const MODE_XOR = 'XOR';

    /**
     * Возвращает режим сложения составных выражений группы
     * @return string
     */
    public function getMode();

    /**
     * Возвращает родительскую группу
     * @internal
     * @return null|IExpressionGroup
     */
    public function getParentGroup();

    /**
     * Добавляет простое выражение
     * @param string $leftCond левая часть выражения
     * @param string $operator оператор
     * @param string $rightCond правая часть выражения
     * @return self
     */
    public function addExpression($leftCond, $operator, $rightCond);

    /**
     * Возвращает массив простых выражений
     * @return array в формате array(array(leftCond, $operator, $rightCond), ...)
     */
    public function getExpressions();

    /**
     * Добавляет дочернюю группу условий
     * @param IExpressionGroup $group группа
     */
    public function addGroup(IExpressionGroup $group);

    /**
     * Возвращает список дочерних групп
     * @return IExpressionGroup[]
     */
    public function getGroups();
}

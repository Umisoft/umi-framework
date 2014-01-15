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

/**
 * Группа условий выборки по значениям полей для селектора.
 * Группа может состоять из вложенных групп и ограничений IFieldCondition
 * Составные группы и простые выражения складываются по OR, AND, XOR
 * в зависимости от указанного режима группы.
 */
interface IFieldConditionGroup
{

    const MODE_AND = 'AND';
    const MODE_OR = 'OR';
    const MODE_XOR = 'XOR';

    /**
     * Возвращает родительскую группу.
     * @return null|IFieldConditionGroup
     */
    public function getParentGroup();

    /**
     * Добавляет ограничение по значению поля.
     * @param IFieldCondition $condition
     * @return IFieldCondition
     */
    public function addCondition(IFieldCondition $condition);

    /**
     * Добавляет дочернюю группу выражений.
     * @param IFieldConditionGroup $group группа
     * @return self
     */
    public function addGroup(IFieldConditionGroup $group);

    /**
     * Возвращает список дочерних групп.
     * @return IFieldConditionGroup[] массив из IFieldConditionGroup
     */
    public function getGroups();

    /**
     * Применяет условия группы к ISelectBuilder
     * @internal
     * @param ISelectBuilder $selectBuilder
     * @return self
     */
    public function applyConditions(ISelectBuilder $selectBuilder);
}

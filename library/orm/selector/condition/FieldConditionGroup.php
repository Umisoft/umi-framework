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
 */
class FieldConditionGroup implements IFieldConditionGroup
{
    /**
     * @var string $mode режим сложения составных выражений
     */
    protected $mode;
    /**
     * @var IFieldCondition[] $conditions список выражений в формате array(array(leftCond, $operator, $rightCond), ...)
     */
    protected $conditions = [];
    /**
     * @var IFieldConditionGroup $parentGroup родительская группа выражений
     */
    private $parentGroup = null;
    /**
     * @var IFieldConditionGroup[] $groups список дочерних групп
     */
    private $groups = [];

    /**
     * Конструктор.
     * @param string $mode режим сложения составных выражений
     * @param null|IFieldConditionGroup $parentGroup родительская группа выражений
     * @return self
     */
    public function __construct($mode = IFieldConditionGroup::MODE_AND, IFieldConditionGroup $parentGroup = null)
    {
        $this->mode = $mode;
        $this->parentGroup = $parentGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    /**
     *{@inheritdoc}
     */
    public function addCondition(IFieldCondition $condition)
    {
        $this->conditions[] = $condition;

        return $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(IFieldConditionGroup $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function applyConditions(ISelectBuilder $selectBuilder)
    {
        $selectBuilder->begin($this->mode);
        foreach ($this->conditions as $fieldCondition) {
            $fieldCondition->apply($selectBuilder);
        }
        foreach ($this->getGroups() as $nextGroup) {
            $nextGroup->applyConditions($selectBuilder);
        }

        $selectBuilder->end();

        return $this;
    }
}

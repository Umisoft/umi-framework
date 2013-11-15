<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

    namespace umi\dbal\builder;

    /**
     * Представляет собой группу выражений.
     * Группа может состоять из вложенных групп и простых выражений
     * Составные группы и простые выражения складываются по OR, AND, XOR
     * в зависимости от указанного режима группы.
     */
    class ExpressionGroup implements IExpressionGroup
    {
        /**
         * @var string $mode режим сложения составных выражений
         */
        protected $mode;

        /**
         * @var array $expressions список выражений в формате array(array(leftCond, $operator, $rightCond), ...)
         */
        protected $expressions = [];
        /**
         * @var IExpressionGroup $parentGroup родительская группа выражений
         */
        private $parentGroup;

        /**
         * @var IExpressionGroup[] $groups список дочерних групп
         */
        private $groups = [];

        /**
         * Конструктор
         * @param string $mode режим сложения составных выражений
         * @param IExpressionGroup $parentGroup родительская группа выражений
         */
        public function __construct($mode = IExpressionGroup::MODE_AND, IExpressionGroup $parentGroup = null)
        {
            $this->mode = strtoupper($mode);
            $this->parentGroup = $parentGroup;
        }

        /**
         * {@inheritdoc}
         */
        public function getMode()
        {
            return $this->mode;
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
        public function addExpression($leftCond, $operator, $rightCond)
        {
            $this->expressions[] = array($leftCond, $operator, $rightCond);
            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function getExpressions()
        {
            return $this->expressions;
        }

        /**
         * {@inheritdoc}
         */
        public function addGroup(IExpressionGroup $group)
        {
            $this->groups[] = $group;
        }

        /**
         * {@inheritdoc}
         */
        public function getGroups()
        {
            return $this->groups;
        }
    }

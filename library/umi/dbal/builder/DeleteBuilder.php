<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use umi\dbal\exception\RuntimeException;

/**
 * Построитель Delete-запросов.
 */
class DeleteBuilder extends BaseQueryBuilder implements IDeleteBuilder
{

    /**
     * @var string $tableName имя таблицы
     */
    protected $tableName;

    /**
     * @var IExpressionGroup $whereExpressionGroup группа условий WHERE
     */
    protected $whereExpressionGroup;

    /**
     * @var integer $limit Ограничение на количество затрагиваемых строк
     */
    protected $limit;

    /**
     * {@inheritdoc}
     */
    public function from($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where($mode = IExpressionGroup::MODE_AND)
    {
        if (!$this->whereExpressionGroup) {
            $this->currentExpressionGroup = null;
            $this->begin($mode);
            $this->whereExpressionGroup = $this->currentExpressionGroup;
        }

        return $this;
    }

    /**
     *{@inheritdoc}
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        if (empty($this->tableName)) {
            throw new RuntimeException($this->translate(
                'Cannot delete from table. Table name required.'
            ));
        }

        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWhereExpressionGroup()
    {
        return $this->whereExpressionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Генерирует и возвращает шаблон Delete-запроса
     * @return string sql
     */
    protected function build()
    {
        return $this->dialect->buildDeleteQuery($this);
    }
}

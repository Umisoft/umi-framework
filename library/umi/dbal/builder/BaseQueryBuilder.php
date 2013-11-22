<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use PDO;
use umi\dbal\driver\IDialect;
use umi\dbal\exception\IException;
use umi\dbal\exception\RuntimeException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый построитель запросов.
 * Все построители запросов должны быть унаследованы от него, содержит
 * шаблонные методы для всех построителей.
 */
abstract class BaseQueryBuilder implements IQueryBuilder, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $expressionGroupClass имя класса группы выражений
     */
    public $expressionGroupClass = 'umi\dbal\builder\ExpressionGroup';
    /**
     * @var Connection $connection Драйвер БД
     */
    protected $connection;
    /**
     * @var IExpressionGroup $currentExpressionGroup текущая группа выражений
     */
    protected $currentExpressionGroup;
    /**
     * @var \Doctrine\DBAL\Statement $preparedStatement подготовленный запрос
     */
    protected $preparedStatement;
    /**
     * @var string $sql Сгенерированный текст запроса
     */
    protected $sql;
    /**
     * @var array $orderConditions список ORDER BY - условий
     */
    protected $orderConditions = [];
    /**
     * @var bool $executed флаг исполненности запроса
     */
    protected $executed = false;
    /**
     * @var IQueryBuilderFactory $queryResultFactory Фабрика запросов и их сущностей
     */
    protected $queryBuilderFactory;
    /**
     * @var array $values массив значений плейсхолдеров вида [':placeholder' => [value, pdoType], ...]
     */
    private $values = [];
    /**
     * @var array $variables массив переменных, связанных с плейсхолдерами
     */
    private $variables = [];
    /**
     * @var array $arrays значения плейсхолдеров, представляющих собой списки IN (1, 2, 3)
     */
    private $arrays = [];
    /**
     * @var array $expressions значения плейсхолдеров, представляющих собой выражения
     */
    private $expressions = [];
    /**
     * @var IDialect $dialect
     */
    protected $dialect;

    /**
     * @var \Doctrine\DBAL\Query\QueryBuilder $doctrineQueryBuilder
     */
    protected $doctrineQueryBuilder;
    /**
     * @var  QueryBuilder $currentDoctrineQuery
     */
    protected $currentDoctrineQuery;
    /**
     * @var  CompositeExpression $currentDoctrineExpression
     */
    protected $currentDoctrineExpression;

    /**
     * Генерирует и возвращает шаблон запроса.
     * Должен быть реализован в конкретном построителе запроса.
     * @return string sql
     */
    abstract protected function build();

    /**
     * Конструктор
     * @param Connection $connection драйвер БД
     * @param IDialect $dialect
     * @param IQueryBuilderFactory $queryBuilderFactory
     * @return BaseQueryBuilder
     */
    public function __construct(Connection $connection, IDialect $dialect, IQueryBuilderFactory $queryBuilderFactory)
    {
        $this->connection = $connection;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->dialect = $dialect;
        $this->doctrineQueryBuilder = $connection->createQueryBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(Connection $connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->connection;
        }
        if (is_null($this->sql)) {
            $this->sql = $this->build($connection);
            $this->sql = $this->prepareArrayPlaceholders($this->sql);
            $this->sql = $this->prepareExpressionPlaceholders($this->sql);
        }

        return $this->sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Начинает новую группу выражений.
     * Группа становится текущей до вызова end
     * @param string $mode режим сложения составных выражений
     * @return $this|SelectBuilder
     */
    public function begin($mode = IExpressionGroup::MODE_AND)
    {
        $parentGroup = $this->currentExpressionGroup;

        $group = $this->queryBuilderFactory->createExpressionGroup($mode, $parentGroup);
        if ($parentGroup) {
            $parentGroup->addGroup($group);
        }

        $this->currentExpressionGroup = $group;

        return $this;
    }

    /**
     * Завершает текущую группу выражений.
     * Текущей становится родительская группа.
     * @return $this|ISelectBuilder
     */
    public function end()
    {
        if ($this->currentExpressionGroup) {
            if (null != ($parentGroup = $this->currentExpressionGroup->getParentGroup())) {
                $this->currentExpressionGroup = $parentGroup;
            } else {
                $this->currentExpressionGroup = null;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExecuted()
    {
        return $this->executed;
    }

    /**
     * Добавляет простое выражение в текущую группу выражений
     * @param string $leftCond
     * @param string $operator
     * @param string $rightCond
     * @throws RuntimeException если не удалось добавить выражение
     * @return $this
     */
    public function expr($leftCond, $operator, $rightCond)
    {
        if (!$this->currentExpressionGroup) {
            throw new RuntimeException($this->translate(
                'Cannot add expression. Expression group is not started.'
            ));
        }
        $this->currentExpressionGroup->addExpression($leftCond, $operator, $rightCond);

        return $this;
    }

    /**
     * Устанавливает условие сортировки
     * @param string $column имя столбца, может быть плейсхолдером
     * @param string $direction направление сортировки, ASC по умолчанию
     * @return $this|ISelectBuilder|IDeleteBuilder|IUpdateBuilder
     */
    public function orderBy($column, $direction = IQueryBuilder::ORDER_ASC)
    {
        if ($direction == IQueryBuilder::ORDER_ASC || $direction == IQueryBuilder::ORDER_DESC) {
            $this->orderConditions[$column] = $direction;
        }

        return $this;
    }

    /**
     * Возвращает список правил сортировки
     * @internal
     * @return array в формате ['columnName' => 'ASC', ...]
     */
    public function getOrderConditions()
    {
        return $this->orderConditions;
    }

    /**
     * Связывает плейсхолдер с значением
     * @internal
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $value значение плейсхолдера
     * @param string $phpType тип плейсхолдера ('string', 'integer', 'boolean', 'array', ...)
     * http://ru2.php.net/manual/en/function.gettype.php
     * @return $this
     */
    public function bindValue($placeholder, $value, $phpType)
    {
        $pdoType = is_null($value) ? PDO::PARAM_NULL : Type::getType($phpType)
            ->getBindingType();
        $this->values[$placeholder] = [$value, $pdoType];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function bindString($placeholder, $value)
    {
        return $this->bindValue($placeholder, strval($value), 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function bindInt($placeholder, $value)
    {
        return $this->bindValue($placeholder, intval($value), 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function bindBool($placeholder, $value)
    {
        return $this->bindValue($placeholder, (bool) $value, 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function bindBlob($placeholder, $value)
    {
        return $this->bindValue($placeholder, $value, 'blob');
    }

    /**
     * {@inheritdoc}
     */
    public function bindFloat($placeholder, $value)
    {
        return $this->bindValue($placeholder, floatval($value), 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function bindNull($placeholder)
    {
        return $this->bindValue($placeholder, null, 'null');
    }

    /**
     * {@inheritdoc}
     */
    public function bindArray($placeholder, array $value)
    {
        if (isset($this->arrays[$placeholder]) && count($this->arrays[$placeholder]) != count($value)) {
            $this->preparedStatement = null; //clear statement, query template modified
            $this->sql = null;
        }
        $this->arrays[$placeholder] = array_values($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function bindExpression($placeholder, $value)
    {
        if (isset($this->expressions[$placeholder]) && $this->expressions[$placeholder] != $value) {
            $this->preparedStatement = null; //clear statement, query template modified
            $this->sql = null;
        }
        $this->expressions[$placeholder] = (string) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholderValues()
    {
        $result = [];
        $values = array_merge($this->values, $this->arrays, $this->expressions);
        foreach ($values as $placeholder => $info) {
            $result[$placeholder] = $info;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function bindVarString($placeholder, &$variable)
    {
        return $this->bindVariable($placeholder, $variable, 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function bindVarInt($placeholder, &$variable)
    {
        return $this->bindVariable($placeholder, $variable, 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function bindVarBool($placeholder, &$variable)
    {
        return $this->bindVariable($placeholder, $variable, 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function bindVarFloat($placeholder, &$variable)
    {
        return $this->bindVariable($placeholder, $variable, 'float');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $sql = $this->getSql();
        if (is_null($this->preparedStatement)) {
            $this->preparedStatement = $this->connection->prepare($sql);
        }

        //todo! fire execute event
        /*$this->fireEvent(
            IConnection::EVENT_AFTER_EXECUTE_QUERY,
            array(
                'queryBuilder'      => $queryBuilder,
                'preparedStatement' => $preparedStatement
            )
        );

        $this->trace(
            'Execute query: {sql} with params {params}.',
            ['sql' => $preparedStatement->queryString, 'params' => $params]
        );*/

        $this->bind($this->preparedStatement, $sql);

        $this->preparedStatement->execute();
        $this->executed = true;

        return $this->preparedStatement;
    }

    /**
     * {@inheritdoc}
     */
    public function getPDOStatement()
    {
        return $this->preparedStatement;
    }

    /**
     * Разбирает алиас в имени таблицы / столбца, если он есть
     * @internal
     * @param string $name
     * @return array в виде ['name', 'alias']
     */
    protected function parseAlias($name)
    {
        if (strpos($name, ' ') !== false && preg_match('/^\s*(.+)(\s+as\s+)(.+)\s*$/i', $name, $matches)) {
            return [$matches[1], $matches[3]];
        }

        return [$name, null];
    }

    /**
     * Связывает плейсхолдер с PHP-переменной.
     * @param string $placeholder плейсхолдер, в формате ':placeholder'
     * @param mixed $variable переменная. Принимается по ссылке, нельзя передавать значение!
     * @param string $phpType тип плейсхолдера ('string', 'integer', 'boolean', ...)
     * @return $this|SelectBuilder|InsertBuilder|UpdateBuilder|DeleteBuilder
     */
    protected function bindVariable($placeholder, &$variable, $phpType = 'string')
    {
        $pdoType = Type::getType($phpType)
            ->getBindingType();
        $this->variables[$placeholder] = [&$variable, $pdoType];

        return $this;
    }

    /**
     * Биндит значения плейсхолдеров
     * @param Statement $preparedStatement
     * @param string $query SQL, сформированный билдером для текущего запроса
     * (например, подзапроса, вложенного в основной)
     */
    protected function bind(Statement $preparedStatement, $query)
    {
        foreach ($this->values as $placeholder => $value) {
            if (strpos($query, $placeholder) !== false) {
                $preparedStatement->bindValue($placeholder, $value[0], $value[1]);
            }
        }

        foreach ($this->variables as $placeholder => $value) {
            if (strpos($query, $placeholder) !== false) {
                $variable = & $value[0];
                $preparedStatement->bindParam($placeholder, $variable, $value[1]);
            }
        }

        foreach ($this->arrays as $placeholder => $values) {
            $count = count($values);
            for ($i = 0; $i < $count; $i++) {
                $value = $values[$i];
                $nextPlaceholder = $placeholder . $i;
                if (is_null($value)) {
                    $preparedStatement->bindValue($nextPlaceholder, $value, \PDO::PARAM_NULL);
                } elseif (is_int($value)) {
                    $preparedStatement->bindValue($nextPlaceholder, $value, \PDO::PARAM_INT);
                } else {
                    $preparedStatement->bindValue($nextPlaceholder, $value, \PDO::PARAM_STR);
                }
            }
        }
    }

    /**
     * Подготавливает запрос, включающий IN (:array) плейсхолдеры
     * @param string $sql
     * @return string $sql
     * @throws IException
     */
    protected function prepareArrayPlaceholders($sql)
    {
        if (!count($this->arrays)) {
            return $sql;
        }
        foreach ($this->arrays as $placeholder => $array) {
            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $placeholders = [];
            $cnt = count($array);
            for ($i = 0; $i < $cnt; $i++) {
                $placeholders[] = $placeholder . $i;
            }
            $sql = str_replace($placeholder, '(' . implode(', ', $placeholders) . ')', $sql);
        }

        return $sql;
    }

    /**
     * Подготавливает запрос, включающий sql-выражения
     * @param string $sql
     * @return string $sql
     */
    protected function prepareExpressionPlaceholders($sql)
    {
        if (!count($this->expressions)) {
            return $sql;
        }
        $sql = str_replace(array_keys($this->expressions), array_values($this->expressions), $sql);

        return $sql;
    }
}

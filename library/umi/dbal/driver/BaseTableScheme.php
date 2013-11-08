<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use umi\dbal\exception\DomainException;
use umi\dbal\exception\NonexistentEntityException;
use umi\dbal\exception\RuntimeException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый класс схемы таблицы в БД.
 */
abstract class BaseTableScheme implements ITableScheme, ILocalizable
{

    use TLocalizable;

    /**
     * @var IDbDriver $dbDriver драйвер БД для схемы
     */
    protected $dbDriver;
    /**
     * @var string $tableName имя таблицы
     */
    protected $tableName;
    /**
     * @var string $collation collation таблицы
     */
    protected $collation = 'utf8_general_ci';
    /**
     * @var string $charset charset таблицы
     */
    protected $charset = 'utf8';
    /**
     * @var string $comment комментарий к таблице
     */
    protected $comment;
    /**
     * @var string $engine engine таблицы
     */
    protected $engine;
    /**
     * @var IColumnScheme[] $columnSchemes загруженные схемы столбцов
     */
    private $columnSchemes = [];
    /**
     * @var IIndexScheme $primaryKey первичный ключ таблицы
     */
    private $primaryKey = false;
    /**
     * @var IIndexScheme[] $indexes загруженные индексы таблицы
     */
    private $indexes;
    /**
     * @var IConstraintScheme[] $constraints загруженные внешние ключи таблицы
     */
    private $constraints;
    /**
     * @var bool $isModified Модифицирована ли схема таблицы
     */
    private $isModified = false;
    /**
     * @var bool $isNew является ли схема таблицы новой
     */
    private $isNew = false;
    /**
     * @var bool $isDeleted является ли схема таблицы удаленной
     */
    private $isDeleted = false;
    /**
     * @var ITableFactory $tableFactory фабрика сущностей таблицы
     */
    private $tableFactory;

    /**
     * Загружает информацию о столбцах таблицы.
     * Должен быть реализован в конкретном драйвере
     * @return IColumnScheme[] массив вида array($columnName => IColumnScheme, ...)
     */
    abstract protected function loadColumns();

    /**
     * Загружает информацию о индексах таблицы.
     * Должен быть реализован в конкретном драйвере
     * @return IIndexScheme[] массив вида array($indexName => IIndexScheme, ... )
     */
    abstract protected function loadIndexes();

    /**
     * Загружает информацию о внешних ключах таблицы.
     * Должен быть реализован в конкретном драйвере
     * @return IConstraintScheme[]
     */
    abstract protected function loadConstraints();

    /**
     * Загружает информацию о первичном ключе таблицы.
     * Должен быть реализован в конкретном драйвере
     * @return IIndexScheme
     */
    abstract protected function loadPrimaryKey();

    /**
     * Извлекает данные о таблице из БД.
     * Должен быть реализован в конкретном драйвере и заполнить необходимые
     * protected - свойства базового класса.
     */
    abstract protected function extract();

    /**
     * Генерирует запросы на создание таблицы.
     * @throws RuntimeException если в процессе генерации возникла ошибка
     * @return array тексты запросов
     */
    abstract protected function generateCreateQueries();

    /**
     * Генерирует запросы на изменение таблицы.
     * @throws RuntimeException если в процессе генерации возникла ошибка
     * @return array текст запросов
     */
    abstract protected function generateAlterQueries();

    /**
     * Генерирует запросы на удаление таблицы.
     * @return array тексты запросов
     */
    abstract protected function generateDropQueries();

    /**
     * Конструктор
     * @param string $tableName имя таблицы
     * @param IDbDriver $driver драйвер БД для схемы
     * @param ITableFactory $tableFactory
     */
    public function __construct($tableName, IDbDriver $driver, ITableFactory $tableFactory)
    {
        $this->tableName = $tableName;
        $this->dbDriver = $driver;
        $this->tableFactory = $tableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsModified($isModified = true)
    {
        $this->isModified = (bool) $isModified;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNew($isNew = true)
    {
        $this->isNew = (bool) $isNew;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeleted($isDeleted = true)
    {
        $this->isDeleted = (bool) $isDeleted;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollation($collation)
    {
        $this->setIsModified(true);
        $this->collation = $collation;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->setIsModified(true);
        $this->comment = $comment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        $this->setIsModified(true);
        $this->charset = $charset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEngine($engine)
    {
        $this->setIsModified(true);
        $this->engine = $engine;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($name, $type, array $options = [])
    {
        if ($this->getColumnExists($name)) {
            return $this->getColumn($name)
                ->setType($type, $options);
        }

        $internalType = $this->dbDriver->getColumnInternalType($type);
        $options = array_merge($options, $this->dbDriver->getColumnTypeOptions($type));

        $this->columnSchemes[$name] = $scheme = $this->createColumnSchemeInstance($name, $internalType, $options);
        $scheme->setIsNew(true);

        return $scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteColumn($name)
    {
        $column = $this->getColumn($name);
        $column->setIsDeleted();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewColumns()
    {
        $result = [];

        foreach ($this->columnSchemes as $name => $scheme) {
            if ($scheme->getIsNew() && !$scheme->getIsDeleted()) {
                $result[$name] = $scheme;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedColumns()
    {
        $result = [];

        foreach ($this->columnSchemes as $name => $scheme) {
            if ($scheme->getIsModified() && !$scheme->getIsDeleted() && !$scheme->getIsNew()) {
                $result[$name] = $scheme;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedColumns()
    {
        $result = [];

        foreach ($this->columnSchemes as $name => $scheme) {
            if ($scheme->getIsDeleted()) {
                $result[$name] = $scheme;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        $this->extract();
        $this->columnSchemes = $this->loadColumns();
        $this->constraints = null;
        $this->indexes = null;
        $this->primaryKey = false;
        $this->setIsModified(false);
        $this->setIsDeleted(false);
        $this->setIsNew(false);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexes()
    {
        if (is_null($this->indexes)) {
            $modified = $this->getIsModified();
            $this->indexes = $this->getIsNew() ? [] : $this->loadIndexes();
            $this->setIsModified($modified);
        }

        return $this->indexes;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        if (is_null($this->constraints)) {
            $modified = $this->getIsModified();
            $this->constraints = $this->getIsNew() ? [] : $this->loadConstraints();
            $this->setIsModified($modified);
        }

        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        if ($this->primaryKey === false) {
            $modified = $this->getIsModified();
            $this->primaryKey = $this->getIsNew() ? null : $this->loadPrimaryKey();
            $this->setIsModified($modified);
        }

        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function deletePrimaryKey()
    {
        if (!$primaryKey = $this->getPrimaryKey()) {
            throw new NonexistentEntityException($this->translate(
                'Primary key does not exist in table "{table}".',
                ['table' => $this->tableName]
            ));
        }
        $primaryKey->setIsDeleted(true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimaryKey($columnName)
    {
        if (!$this->getPrimaryKey()) {
            $this->primaryKey = $this->createIndexSchemeInstance('PRIMARY', true);
            $this->primaryKey->setIsNew(true);
            $this->primaryKey->setIsUnique(true);
        }
        $newColumns = func_get_args();
        $columns = $this->primaryKey->getColumns();
        foreach ($columns as $column) {
            if (!in_array($column['name'], $newColumns)) {
                $nextCol = $this->getColumn($column['name']);
                $nextCol->setOption(IColumnScheme::OPTION_PRIMARY_KEY, false);
                $nextCol->setOption(IColumnScheme::OPTION_AUTOINCREMENT, false);
                $this->primaryKey->deleteColumn($column['name']);
            }
        }
        foreach ($newColumns as $columnName) {
            $this->primaryKey->addColumn($columnName);
            $this->getColumn($columnName)
                ->setOption(IColumnScheme::OPTION_PRIMARY_KEY, true);
        }

        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex($name)
    {
        if (!$this->getIndexExists($name)) {
            throw new NonexistentEntityException($this->translate(
                'Index "{index}" does not exist in table "{table}".',
                ['table' => $this->tableName, 'index' => $name]
            ));
        }

        /**
         * @var IndexScheme $index
         */

        return $index = $this->indexes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function addIndex($name)
    {
        if ($this->getIndexExists($name)) {
            return $this->getIndex($name);
        }
        $index = $this->createIndexSchemeInstance($name)
            ->setIsNew(true);

        return $this->indexes[$name] = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex($name)
    {
        $this->getIndex($name)
            ->setIsDeleted(true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexExists($name)
    {
        $indexes = $this->getIndexes();

        return isset($indexes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewIndexes()
    {
        $result = [];

        foreach ($this->getIndexes() as $name => $index) {
            if ($index->getIsNew() && !$index->getIsDeleted()) {
                $result[$name] = $index;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedIndexes()
    {
        $result = [];

        foreach ($this->getIndexes() as $name => $index) {
            if ($index->getIsModified() && !$index->getIsNew() && !$index->getIsDeleted()) {
                $result[$name] = $index;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedIndexes()
    {
        $result = [];

        foreach ($this->getIndexes() as $name => $index) {
            if ($index->getIsDeleted()) {
                $result[$name] = $index;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint($name)
    {
        if (!$this->getConstraintExists($name)) {
            throw new NonexistentEntityException($this->translate(
                'Constraint "{constraint}" does not exist in table "{table}".',
                ['table' => $this->tableName, 'constraint' => $name]
            ));
        }

        return $this->constraints[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(
        $name,
        $columnName,
        $referenceTableName,
        $referenceColumnName,
        $onDeleteAction = null,
        $onUpdateAction = null
    )
    {

        if ($this->getConstraintExists($name)) {
            $constraint = $this->getConstraint($name);
        } else {
            $constraint = $this->createConstraintSchemeInstance($name);
            $constraint->setIsNew(true);
            $this->constraints[$name] = $constraint;
        }
        $constraint->setColumnName($columnName)
            ->setReferenceTableName($referenceTableName)
            ->setReferenceColumnName($referenceColumnName)
            ->setOnDeleteAction($onDeleteAction)
            ->setOnUpdateAction($onUpdateAction);

        return $constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteConstraint($name)
    {
        $this->getConstraint($name)
            ->setIsDeleted(true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintExists($name)
    {
        $constraints = $this->getConstraints();

        return isset($constraints[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewConstraints()
    {
        $result = [];
        foreach ($this->getConstraints() as $name => $constraint) {
            if ($constraint->getIsNew() && !$constraint->getIsDeleted()) {
                $result[$name] = $constraint;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedConstraints()
    {
        $result = [];

        foreach ($this->getConstraints() as $name => $constraint) {
            if ($constraint->getIsDeleted()) {
                $result[$name] = $constraint;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedConstraints()
    {
        $result = [];

        foreach ($this->getConstraints() as $name => $constraint) {
            if ($constraint->getIsModified() && !$constraint->getIsDeleted() && !$constraint->getIsNew()) {
                $result[$name] = $constraint;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationQueries()
    {
        if ($this->isNew && !$this->getIsDeleted()) {
            return $this->generateCreateQueries();
        } elseif ($this->isModified && !$this->getIsDeleted()) {
            return $this->generateAlterQueries();
        } elseif ($this->isDeleted && !$this->isNew) {
            return $this->generateDropQueries();
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if (!isset($this->columnSchemes[$name])) {
            throw new NonexistentEntityException($this->translate(
                'Column "{column}" does not exist in table "{table}".',
                ['table' => $this->tableName, 'column' => $name]
            ));
        }

        return $this->columnSchemes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnExists($name)
    {
        return isset($this->columnSchemes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columnSchemes;
    }

    /**
     * Создает и возвращает схему столбца
     * @param string $name имя столбца
     * @param string $internalType внутренний тип столбца
     * @param array $options список параметров столбца
     * @throws DomainException если не удалось создать столбец
     * @return IColumnScheme
     */
    protected function createColumnSchemeInstance($name, $internalType, array $options = [])
    {
        return $this->tableFactory->createColumn($name, $internalType, $options, $this->dbDriver, $this);
    }

    /**
     * Создает и возвращает схему индекса
     * @param string $name имя индекса
     * @throws DomainException если не удалось создать индекс
     * @return IIndexScheme
     */
    protected function createIndexSchemeInstance($name)
    {
        return $this->tableFactory->createIndex($name, $this);
    }

    /**
     * Создает и возвращает схему внешнего ключа
     * @param string $name имя индекса
     * @throws DomainException если не удалось создать внешний ключ
     * @return IConstraintScheme
     */
    protected function createConstraintSchemeInstance($name)
    {
        return $this->tableFactory->createConstraint($name, $this);
    }
}

<?php
namespace umi\dbal\builder;

/**
 * Построитель Join - секции запроса.
 */
class JoinBuilder implements IJoinBuilder
{
    /**
     * @var array $table имя таблицы в формате array('name', 'alias')
     */
    protected $table;
    /**
     * @var string $type тип JOIN
     */
    protected $type;
    /**
     * @var array $conditions условия JOIN
     */
    protected $conditions = [];

    /**
     * Конструктор.
     * @param string|array $table имя таблицы для джойна, может быть массивом вида array('name', 'alias');
     * @param string $type - тип джойна
     */
    public function __construct($table, $type = 'INNER')
    {
        if (is_string($table)) {
            $this->table = $this->parseAlias($table);
        } else {
            $this->table = (array) $table;
        }
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function on($leftColumn, $operator, $rightColumn)
    {
        $this->conditions[] = array($leftColumn, $operator, $rightColumn);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Разбирает алиас в имени таблицы / столбца, если он есть
     * @internal
     * @param string $name
     * @return array в виде array('name', 'alias')
     */
    protected function parseAlias($name)
    {
        if (strpos($name, ' ') !== false && preg_match('/^\s*(.+)(\s+as\s+)(.+)\s*$/i', $name, $matches)) {
            return array($matches[1], $matches[3]);
        }

        return array($name, null);
    }
}

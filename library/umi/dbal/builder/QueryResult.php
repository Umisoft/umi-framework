<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

    namespace umi\dbal\builder;

    use Doctrine\DBAL\Connection;
    use PDO;
    use PDOStatement;

    /**
     * Предоставляет интерфейс доступа к результатам запроса.
     * Обертка для PDOStatement.
     */
    class QueryResult implements IQueryResult
    {
        /**
         * @var Connection $connection драйвер БД
         */
        private $connection;
        /**
         * @var array $rows массив строк результата
         */
        protected $rows;
        /**
         * @var PDOStatement $pdoStatement Подготовленный запрос
         */
        private $pdoStatement;
        /**
         * @var array $resultVariables массив переменных, связанных с результатом
         */
        private $resultVariables = [];
        /**
         * @var int $count количество строк для SELECT, либо кол-во затронутых рядов для INSERT/UPDATE
         */
        private $count;

        /**
         * Конструктор.
         * @param IQueryBuilder $query запрос
         * @param array $resultVariables массив переменных, связанных с результатом
         */
        public function __construct(IQueryBuilder $query, array $resultVariables)
        {
            $this->connection = $query->getConnection();
            $this->pdoStatement = $query->getPDOStatement();
            $this->resultVariables = $resultVariables;
            $this->fetchAll();
        }

        /**
         * {@inheritdoc}
         */
        public function debugInfo()
        {
            $this->pdoStatement->debugDumpParams();
        }

        /**
         * {@inheritdoc}
         */
        public function fetchAll()
        {
            if (!is_null($this->rows)) {
                return $this->rows;
            }

            if (stripos(ltrim($this->pdoStatement->queryString), 'SELECT') === 0) {
                $this->rows = $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC);
                $this->count = count($this->rows);
                for ($i = 0; $i < $this->count; $i++) {
                    foreach ($this->resultVariables as $columnName => $varInfo) {
                        if (!isset($this->rows[$i][$columnName])) {
                            continue;
                        }
                        $varType = $varInfo[1];
                        settype($this->rows[$i][$columnName], $varType);
                    }
                }
            } else {
                $this->rows = [];
                $this->count = $this->pdoStatement->rowCount();
            }
            $this->pdoStatement->closeCursor();

            return $this->rows;
        }

        /**
         * {@inheritdoc}
         */
        public function fetch()
        {
            $row = current($this->rows);
            if ($row === false) {
                return false;
            }

            foreach ($this->resultVariables as $columnName => $varInfo) {
                $var = & $varInfo[0];
                if (isset($row[$columnName])) {
                    $var = $row[$columnName];
                } else {
                    $var = null;
                }
            }

            next($this->rows);
            return $row;
        }

        /**
         * {@inheritdoc}
         */
        public function fetchVal()
        {
            if (!isset($this->rows[0]) || count($this->rows[0]) == 0) {
                return null;
            }

            return current($this->rows[0]);
        }

        /**
         * {@inheritdoc}
         */
        public function lastInsertId($name = null)
        {
            return (int) $this->connection->getPDO()->lastInsertId($name);
        }

        /**
         * {@inheritdoc}
         */
        public function current()
        {
            return current($this->rows);
        }

        /**
         * {@inheritdoc}
         */
        public function next()
        {
            $this->fetch();
        }

        /**
         * {@inheritdoc}
         */
        public function key()
        {
            return key($this->rows);
        }

        /**
         * {@inheritdoc}
         */
        public function valid()
        {
            $key = key($this->rows);
            return ($key !== null && $key !== false);
        }

        /**
         * {@inheritdoc}
         */
        public function rewind()
        {
            reset($this->rows);
        }

        /**
         * Количество строк для SELECT, либо кол-во затронутых рядов для INSERT/UPDATE
         * @return integer
         */
        public function count()
        {
            return $this->countRows();
        }

        /**
         * {@inheritdoc}
         */
        public function countRows()
        {
            return $this->count;
        }
    }

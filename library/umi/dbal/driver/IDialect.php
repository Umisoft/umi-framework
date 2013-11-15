<?php
    namespace umi\dbal\driver;

    use Doctrine\DBAL\Connection;
    use Doctrine\DBAL\DBALException;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use PDO;
    use umi\dbal\builder\IDeleteBuilder;
    use umi\dbal\builder\IInsertBuilder;
    use umi\dbal\builder\ISelectBuilder;
    use umi\dbal\builder\IUpdateBuilder;
    use umi\dbal\exception\IException;

    interface IDialect
    {
        /**
         * Инициализирует PDO, используя специфику драйвера.
         * Может быть переопределен в конкретном драйвере.
         * @param Connection|IConnection $connection
         * @param PDO $pdo
         * @return
         */
        public function initPDOInstance(Connection $connection, PDO $pdo);

        /**
         * Строит и возвращает sql-запрос для отключения индексов в отдельной таблице
         * @param string $tableName
         * @return string
         */
        public function getDisableKeysSQL($tableName);

        /**
         * Строит и возвращает sql-запрос для включения индексов в отдельной таблице
         * @param string $tableName
         * @return string
         */
        public function getEnableKeysSQL($tableName);

        /**
         * Строит и возвращает sql-запрос для отключения проверки внешних ключей в бд
         * @return string
         */
        public function getDisableForeignKeysSQL();

        /**
         * Строит и возвращает sql-запрос для включения проверки внешних ключей в бд
         * @return string
         */
        public function getEnableForeignKeysSQL();

        /**
         * Строит и возвращает sql-запрос на выборку данных
         * @param ISelectBuilder $query select-запрос
         * @throws IException если не удалось построить запрос
         * @return string
         */
        public function buildSelectQuery(ISelectBuilder $query);

        /**
         * Строит и возвращает sql-запрос на получение количества записей, удовлетворяюших SELECT-запросу
         * @param ISelectBuilder $query select-запрос
         * @throws IException если не удалось построить запрос
         * @return string
         */
        public function buildSelectFoundRowsQuery(ISelectBuilder $query);

        /**
         * Строит и возвращает sql-запрос на обновление данных.
         * @param IUpdateBuilder $query update-запрос
         * @throws IException если не удалось построить запрос
         * @return string
         */
        public function buildUpdateQuery(IUpdateBuilder $query);

        /**
         * Строит и возвращает sql-запрос на вставку данных.
         * @param IInsertBuilder $query insert-запрос
         * @throws IException если не удалось построить запрос
         * @return string
         */
        public function buildInsertQuery(IInsertBuilder $query);

        /**
         * Строит и возвращает sql-запрос на удаление данных
         * @param IDeleteBuilder $query delete-запрос
         * @throws IException если не удалось построить запрос
         * @return string
         */
        public function buildDeleteQuery(IDeleteBuilder $query);

        /**
         * Возвращает запрос на удаление таблицы.
         * По сравнению с Doctrine платформой, поддерживает флаг ifExists
         * @param string $table Имя таблицы
         * @param bool $ifExists Добавить к запросу проверку на существование
         * @return string
         */
        public function getDropTableSQL($table, $ifExists = true);

    }

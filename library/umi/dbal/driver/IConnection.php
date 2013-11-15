<?php
    namespace umi\dbal\driver;

    use Doctrine\DBAL\Driver\Connection;

    /**
     * Соединение с БД, унаследованное от Doctrine\DBAL\Connection.
     */
    interface IConnection extends Connection
    {
        /**
         * Декорированное соединение с PDO драйвером
         * @return \Doctrine\DBAL\Driver\PDOConnection
         */
        public function getWrappedConnection();

        /**
         * Параметры, передаваемые драйверу PDO при создании
         * @return array
         */
        public function getParams();
    }

<?php
/**
 * Словарь для локализации системных сообщений базы данных
 */
return array(

    'Class "{class}" should implement "{interface}".'                             => 'Класс "{class}" должен реализовывать интерфейс "{interface}".',
    //DbCluster
    'Database server "{server}" not found.'                                       => 'Сервер баз данных "{server}" не найден.',
    'Cannot detect master server for db connection.'                              => 'Не удалось определить мастер-сервер для подключения к базе данных.',
    //BaseQueryBuilder
    'Cannot add expression. Expression group is not started.'                     => 'Не удалось добавить выражение выборки. Группа выражений не определена.',
    //DeleteBuilder
    'Cannot delete from table. Table name required.'                              => 'Для удаления данных из таблицы необходимо имя таблицы',
    //InsertBuider
    'Cannot insert into table. Table name required.'                              => 'Для вставки данных в таблицу необходимо имя таблицы',
    'Cannot insert into table "{table}". Value for at least one column required.' => 'Для вставки данных в таблицу "{table}" необходимо указать значение хотя бы для одного столбца.',
    //UpdateBuider
    'Cannot update table. Table name required.'                                   => 'Для обновления данных в таблице необходимо имя таблицы',
    'Cannot update table "{table}". Value for at least one column required.'      => 'Для обновления данных в таблице "{table}" необходимо указать значение хотя бы для одного столбца.',
    //SelectBuilder
    'Cannot select from tables. Table names required.'                            => 'Для выборки данных из таблиц необходимы имена таблиц.',
    //BaseDriver
    'Select query error: "{message}". (dsn: "{dsn}").'                            => 'Не удалось выполнить select-запрос: "{message}". (Подключение: "{dsn}").',
    'Modify query error: "{message}". (dsn: "{dsn}").'                            => 'Не удалось выполнить update-запрос: "{message}". (Подключение: "{dsn}").',
    'Driver "{driver}" does not support column type "{type}".'                    => 'Драйвер "{driver}" не поддерживает тип колонок "{type}".',
    'Cannot prepare SQL "{sql}": {message}'                                       => 'Не удалось подготовить SQL-запрос "{sql}": {message}',
    'Cannot execute query "{query}": {message}'                                   => 'Не удалось выполнить запрос "{query}": {message}',
    'Failed to open DB connection: dsn string cannot be empty.'                   => 'Не удалось подключить к базе данных. Строка с соединением пустая.',
    'Failed to open DB connection to "{dsn}": {message}'                          => 'Не удалось подключить к базе данных c подключением "{dsn}": {message}',
    //BaseTableScheme
    'Primary key does not exist in table "{table}".'                              => 'Первичный ключ в таблице "{table}" отсутствует.',
    'Index "{index}" does not exist in table "{table}".'                          => 'Индекс "{index}" отсутствует в таблице "{table}".',
    'Constraint "{constraint}" does not exist in table "{table}".'                => 'Внешний ключ "{constraint}" отсутствует в таблице "{table}".',
    'Column "{column}" does not exist in table "{table}".'                        => 'Столбец "{column}" отсутствует в таблице "{table}".',
    //MySql, Sqlite
    'Cannot load table "{table}" for "{dsn}": {message}'                          => 'Не удалось загрузить таблицу "{table}" для "{dsn}": {message}',
    'Cannot load tables for "{dsn}": {message}'                                   => 'Не удалось загрузить таблицы для "{dsn}": {message}',
    //MySqlTable, SqliteTable
    'Cannot create table "{table}" without columns.'                              => 'Невозможно создать таблицу "{table}" без колонок.',
    //SqliteTable
    'Cannot load columns for table "{table}".'                                    => 'Не удалось загрузить столбцы для таблицы "{table}".',
    //Sqlite
    'Sqlite driver does not support \'alter table ... disable keys\' queries.'    => 'Sqlite не поддерживает запросы на отключение индексирования данных в таблице.',
    'Sqlite driver does not support \'alter table ... enable keys\' queries.'     => 'Sqlite не поддерживает запросы на включение индексирования данных в таблице.',
    'Sqlite driver does not support foreign keys deleting.'                       => 'Sqlite не поддерживает запросы на удаление внешних ключей таблицы.',
    'Sqlite driver does not support keys deleting.'                               => 'Sqlite не поддерживает запросы на удаление индексов таблицы.',
    'Sqlite driver does not support column deleting.'                             => 'Sqlite не поддерживает запросы на удаление столбцов таблицы.',
    'Sqlite driver does not support primary key deleting.'                        => 'Sqlite не поддерживает запросы на удаление первичного ключа таблицы.',
    'Sqlite driver does not support keys modification.'                           => 'Sqlite не поддерживает запросы на изменение индексов таблицы.',
    'Sqlite driver does not support columns modification.'                        => 'Sqlite не поддерживает запросы наизменение столбцов таблицы.',
    'Sqlite driver does not support foreign keys modification.'                   => 'Sqlite не поддерживает запросы на изменение внешних ключей таблицы.',
    'Sqlite driver does not support primary key adding.'                          => 'Sqlite не поддерживает запросы на добавление первичного ключа в существующую таблицу.',
    'Sqlite driver does not support foreign keys adding.'                         => 'Sqlite не поддерживает запросы на добавление внешних ключей в существующую таблицу.',

);
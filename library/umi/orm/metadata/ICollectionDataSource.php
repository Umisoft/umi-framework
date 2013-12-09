<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use Doctrine\DBAL\Connection;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\cluster\server\IMasterServer;
use umi\dbal\cluster\server\ISlaveServer;
use umi\dbal\exception\RuntimeException;
use umi\orm\exception\DomainException;

/**
 * Источник данных для коллекции объектов.
 * Позволяет делать низкоуровневые операции над таблицой БД,
 * которая является источником данных коллекции.
 */
interface ICollectionDataSource
{
    /**
     * Возвращает имя источника данных
     * @throws DomainException если имя источника не установлено
     * @return string
     */
    public function getSourceName();

    /**
     * Возвращает используемый master-сервер (для модификации данных)
     * @throws RuntimeException если не удалось получить master-сервер
     * @return IMasterServer
     */
    public function getMasterServer();

    /**
     * Возвращает идентификатор конкретного мастер-сервера (для модификации данных)
     * @return string|null null, если конкретный сервер не установлен
     */
    public function getMasterServerId();

    /**
     * Возвращает используемый slave-сервер (для выборки данных)
     * @throws RuntimeException если не удалось получить slave-сервер
     * @return ISlaveServer
     */
    public function getSlaveServer();

    /**
     * Возвращает идентификатор конкретного slave-сервера (для выборки данных)
     * @return string|null null, если конкретный сервер не установлен
     */
    public function getSlaveServerId();

    /**
     * Возвращает драйвер БД, используемый источником
     * @return Connection
     */
    public function getConnection();

    /**
     * Подготавливает запрос на выборку данных из источника,
     * определить список столбцов для выборки. <br />
     * Список столбцов передается в параметрах метода.<br />
     * Если столбцы не переданы, будет сформирован запрос, содержащий все столбцы (SELECT *)<br />
     *
     * @param array $columns
     *
     * @return ISelectBuilder
     */
    public function select($columns = []);

    /**
     * Подготавливает запрос на вставку данных в источник
     * @param bool $isIgnore игнорировать ошибки и duplicate-key конфликты
     * @return IInsertBuilder
     */
    public function insert($isIgnore = false);

    /**
     * Подготавливает запрос на обновление данных источника
     * @param bool $isIgnore игнорировать ошибки и duplicate-key конфликты
     * @return IUpdateBuilder
     */
    public function update($isIgnore = false);

    /**
     * Подготавливает запрос на удаление данных из источника
     * @return IDeleteBuilder
     */
    public function delete();
}

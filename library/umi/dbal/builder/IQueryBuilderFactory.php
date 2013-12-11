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
use umi\dbal\driver\IDialect;
use umi\dbal\exception\DomainException;

/**
 * Фабрика для построителей запросов и их сущностей.
 */
interface IQueryBuilderFactory
{

    /**
     * Создает и возвращает экземпляр построителя INSERT-запросов
     * @param Connection $connection
     * @param IDialect $dialect
     * @return IInsertBuilder
     */
    public function createInsertBuilder(Connection $connection, IDialect $dialect);

    /**
     * Создает и возвращает экземпляр построителя DELETE-запросов
     * @param Connection $connection
     * @param IDialect $dialect
     * @return IDeleteBuilder
     */
    public function createDeleteBuilder(Connection $connection, IDialect $dialect);

    /**
     * Создает и возвращает экземпляр построителя UPDATE-запросов
     * @param Connection $connection
     * @param IDialect $dialect
     * @return IUpdateBuilder
     */
    public function createUpdateBuilder(Connection $connection, IDialect $dialect);

    /**
     * Создает и возвращает экземпляр построителя SELECT-запросов
     * @param Connection $connection
     * @param IDialect $dialect
     * @return ISelectBuilder
     */
    public function createSelectBuilder(Connection $connection, IDialect $dialect);

    /**
     * Создаёт билдер JOIN таблицы.
     * @param array|string $table имя таблицы для джойна, может быть массивом вида array('name', 'alias');
     * @param string $type тип (LEFT, INNER, ...). По умолчанию INNER
     * @return self
     */
    public function createJoinBuilder($table, $type);

    /**
     * Создает и возвращает группу выражений
     * @param string $mode режим сложения составных выражений
     * @param null|IExpressionGroup $parentGroup родительская группа выражений
     * @throws DomainException если не удалось создать таблицу
     * @return IExpressionGroup
     */
    public function createExpressionGroup($mode = IExpressionGroup::MODE_AND, IExpressionGroup $parentGroup = null);

    /**
     * Создает и возвращает интерфейс доступа к результатам запроса
     * @param IQueryBuilder $query запрос
     * @param array $resultVariables массив переменных, связанных с результатом
     * @throws DomainException если не удалось создать доступ
     * @return IQueryResult
     */
    public function createQueryResult(IQueryBuilder $query, array $resultVariables);
}

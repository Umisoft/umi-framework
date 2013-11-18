<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

/**
 * �?нтерфейс результата запроса.
 */
interface IQueryResult extends \Iterator, \Countable
{
    /**
     * Выводит информацию о запросе для дебага в stdout
     */
    public function debugInfo();

    /**
     * Возвращает весь массив строк результата
     * @return array
     */
    public function fetchAll();

    /**
     * Возвращает информацию о следующей строке результата.
     * @return array|boolean массив данных строки,
     * либо false, если достигли конца результата
     */
    public function fetch();

    /**
     * Возвращает первое значение из первого кортежа.
     * @return mixed
     */
    public function fetchVal();

    /**
     * Возвращает идентификатор последней вставленной строки
     * @param string|null $name name of the sequence object from which the ID should be returned
     * @return integer
     */
    public function lastInsertId($name = null);

    /**
     * Количество строк для SELECT, либо кол-во затронутых рядов для INSERT/UPDATE
     * @return integer
     */
    public function countRows();
}

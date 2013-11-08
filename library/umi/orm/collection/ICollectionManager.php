<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\DomainException;
use umi\orm\exception\NonexistentEntityException;

/**
 * Менеджер коллекций.
 */
interface ICollectionManager
{
    /**
     * Проверяет, зарегистрирована ли коллекция объектов
     * @param string $collectionName имя коллекции
     * @return boolean
     */
    public function hasCollection($collectionName);

    /**
     * Возвращает экземпляр коллекции
     * @param string $collectionName уникальное имя коллекции объектов
     * @throws NonexistentEntityException если не существует коллекции
     * @throws DomainException если не удалось создать экземпляр коллекции
     * @return ISimpleCollection|IHierarchicCollection|ILinkedHierarchicCollection|ICommonHierarchy
     */
    public function getCollection($collectionName);

    /**
     * Возвращает список имен зарегистрированных коллекций
     * @return array
     */
    public function getList();

}

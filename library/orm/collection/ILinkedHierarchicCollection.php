<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\RuntimeException;

/**
 * Коллекция связанных иерархических объектов.
 * Особоый вид иерархической коллекции, которая связана с общей иерархией (ICommonHierarchy).
 */
interface ILinkedHierarchicCollection extends ISimpleHierarchicCollection
{

    /**
     * Устанавливает общую иерархию, с которой связана данная коллекция
     * @internal
     * @param ICommonHierarchy $hierarchy
     * @return self
     */
    public function setCommonHierarchy(ICommonHierarchy $hierarchy);

    /**
     * Возвращает общую иерархию, с которой связана данная коллекция
     * @throws RuntimeException если общая иерархия не была внедрена
     * @return ICommonHierarchy
     */
    public function getCommonHierarchy();
}

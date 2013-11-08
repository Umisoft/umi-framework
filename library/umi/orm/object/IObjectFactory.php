<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object;

use umi\orm\collection\ICollection;
use umi\orm\metadata\IObjectType;

/**
 * Фабрика объектов данных.
 */
interface IObjectFactory
{
    /**
     * Создает экземпляр объекта данных.
     * @param ICollection $collection коллекция, которой принадлежит объект
     * @param IObjectType $objectType тип объекта
     * @return IObject|IHierarchicObject
     */
    public function createObject(ICollection $collection, IObjectType $objectType);

    /**
     * Восстанавливает объект.
     * @param IObject $object объект
     * @param ICollection $collection коллекция объекта
     * @param IObjectType $objectType тип объекта
     */
    public function wakeUpObject(IObject $object, ICollection $collection, IObjectType $objectType);
}

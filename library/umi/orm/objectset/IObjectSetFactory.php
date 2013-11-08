<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\object\IObject;

/**
 * Фабрика наборов объектов.
 */
interface IObjectSetFactory
{

    /**
     * Создает набор объектов коллекции
     * @return IObjectSet
     */
    public function createObjectSet();

    /**
     * Создает пустой набор объектов коллекции
     * @return IEmptyObjectSet
     */
    public function createEmptyObjectSet();

    /**
     * Создает набор объектов для свойства типа relation с типом связи manyToMany.
     * @param IObject $object
     * @param ManyToManyRelationField $manyToManyRelationField
     * @return IManyToManyObjectSet
     */
    public function createManyToManyObjectSet(IObject $object, ManyToManyRelationField $manyToManyRelationField);

}

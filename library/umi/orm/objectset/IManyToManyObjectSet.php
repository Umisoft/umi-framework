<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

use umi\orm\exception\AlreadyExistentEntityException;
use umi\orm\exception\IException;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\exception\RuntimeException;
use umi\orm\object\IObject;

/**
 * Набор объектов для свойства типа relation с типом связи manyToMany.
 * Предоставляет доступ к объектам с возможностью выставлять и удалять связи из набора
 */
interface IManyToManyObjectSet extends IObjectSet
{

    /**
     * Добавляет объект target-коллекции в набор значений свойства
     * @param IObject $object добавляемый объект
     * @throws AlreadyExistentEntityException если объект уже существует в наборе
     * @throws RuntimeException если не удалось добавить объект
     * @return IObject объект содержащий в себе свойства связи (объект bridge-коллекции)
     */
    public function attach(IObject $object);

    /**
     * Добавляет объект target-коллекции в набор значений свойства либо возвращает существующий
     * @param IObject $object добавляемый объект
     * @throws IException если не удалось добавить объект
     * @return IObject объект содержащий в себе свойства связи (объект bridge-коллекции)
     */
    public function link(IObject $object);

    /**
     * Проверяет содержится ли объект target-коллекции в наборе значений свойства
     * @param IObject $object объект
     * @throws InvalidArgumentException если проверяется объект из неподходящей коллекции
     * @return bool
     */
    public function contains(IObject $object);

    /**
     * Удаляет объект target-коллекции из набора значений свойства
     * @param IObject $object удаляемый объект
     * @return self
     */
    public function detach(IObject $object);

    /**
     * Удаляет все объекты target-коллекции из набора значений свойства
     * @return IObject $this
     */
    public function detachAll();

}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\IException;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;

/**
 * Коллекция простых объектов.
 */
interface ISimpleCollection extends ICollection
{

    /**
     * Добавляет объект в коллекцию и возвращает его экземпляр.
     * @param string $typeName имя дочернего типа.
     * Если тип не указан, будет создан объект базового типа
     * @throws IException если не удалось добавить объект
     * @return IObject
     */
    public function add($typeName = IObjectType::BASE);
}

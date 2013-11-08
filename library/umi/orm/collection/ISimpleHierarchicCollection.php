<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\NonexistentEntityException;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;

/**
 * Интерфейс простой иерархической коллекции.
 */
interface ISimpleHierarchicCollection extends IHierarchicCollection
{
    /**
     * Добавляет иерархический объект в коллекцию и возвращает его экземпляр.
     * @param string $slug последняя часть ЧПУ
     * @param string $typeName имя типа
     * @param IHierarchicObject|null $branch ветка, в которую добавляется объект
     * @throws NonexistentEntityException если тип не существует
     * @return IHierarchicObject
     */
    public function add($slug, $typeName = IObjectType::BASE, IHierarchicObject $branch = null);
}

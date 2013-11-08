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
 * Общая иерархия - особая коллекция разнотипных иерархически связанных объектов.
 * Иерархия предназначена для построения общих деревьев разнотипных объектов, а также является
 * поставщиком и генератором идентификаторов для объектов связанных с ней иерархических коллекций (ILinkedHierarchicCollection).
 * Иерархия может содержать общий набор полей для связанных с ней коллекций (денормализация),
 * что обеспечивает быструю иерархическую выборку разнотипных объектов.
 * Значения общих полей дублируются в иерархию автоматически.
 * Наиболее наглядное практическое применение - построение общей структуры сайта.
 * Общая иерархия доступна только на выборки и возвращает набор объектов с частично загруженными (общими) свойствами.
 * Каждый объект принадлежит собственной иерархической коллекции, при попытке обратиться к уникальным свойствам объекта,
 * он будет догружен из соответсвующей коллекции.
 */
interface ICommonHierarchy extends IHierarchicCollection
{

    /**
     * Добавляет иерархический объект в коллекцию и возвращает его экземпляр.
     * @param ILinkedHierarchicCollection $linkedCollection коллекция, которой принадлежит объект
     * @param string $slug последняя часть ЧПУ
     * @param string $typeName имя типа
     * @param IHierarchicObject|null $branch ветка, в которую добавляется объект
     * @throws NonexistentEntityException если тип не существует
     * @return IHierarchicObject
     */
    public function add(
        ILinkedHierarchicCollection $linkedCollection,
        $slug,
        $typeName = IObjectType::BASE,
        IHierarchicObject $branch = null
    );
}

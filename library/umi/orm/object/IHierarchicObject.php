<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object;

/**
 * Иерархический объект.
 * Иерархический объект - это объект, который имеет родственные связи.
 */
interface IHierarchicObject extends IObject
{
    /**
     * Имя поля, используемого для хранения информации о родителе объекта
     */
    const FIELD_PARENT = 'parent';
    /**
     * Имя поля, используемого для хранения информации о всех родителях объекта
     */
    const FIELD_MPATH = 'mpath';
    /**
     * Имя поля, используемого для хранения глубины в иерархии
     */
    const FIELD_HIERARCHY_LEVEL = 'level';
    /**
     * Имя поля, используемого для хранения информации о иерархическом порядке
     */
    const FIELD_ORDER = 'order';
    /**
     * Имя поля, используемого для хранения количества непосредственных детей иерархических объектов
     */
    const FIELD_CHILD_COUNT = 'childCount';
    /**
     * Имя поля, используемого для хранения последней части ЧПУ
     */
    const FIELD_SLUG = 'slug';
    /**
     * Имя поля, используемого для хранения url
     */
    const FIELD_URI = 'uri';

    /**
     * Возвращает родительский объект
     * @return IHierarchicObject|null
     */
    public function getParent();

    /**
     * Возвращает материализованный путь объекта
     * @return string
     */
    public function getMaterializedPath();

    /**
     * Возвращает порядок следования в иерархии
     * @return int
     */
    public function getOrder();

    /**
     * Возвращает уровень вложенности в иерархии
     * @return int
     */
    public function getLevel();

    /**
     * Возвращает количество непостредственных детей
     * @return int
     */
    public function getChildCount();

    /**
     * Возвращает URI
     * @return string
     */
    public function getURI();

    /**
     * Возвращает URL
     * @return string
     */
    public function getURL();

    /**
     * Возвращает последнюю часть ЧПУ
     * @return string
     */
    public function getSlug();
}

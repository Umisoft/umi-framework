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
use umi\orm\metadata\field\IField;
use umi\orm\object\IHierarchicObject;
use umi\orm\selector\ISelector;

/**
 * Коллекция иерархических объектов. Предназначена для хранения объектов, имеющих родственные связи.
 * Иерархическая коллекция имеет набор обязательных полей для поддержания связи, а так же специфичные
 * методы для управления такими связями.
 * Простой пример иерархической коллекции - меню сайта, где каждый элемент меню - объект коллекции, который
 * имеет родителя и детей.
 */
interface IHierarchicCollection extends ICollection
{

    /**
     * Возвращает максимальный порядок в указанной ветке.
     * @param IHierarchicObject|null $branch если ветка не указана, вернет максимальный порядок в корне
     * @return int
     */
    public function getMaxOrder(IHierarchicObject $branch = null);

    /**
     * Перемещает объект по иерархии после указанного объекта.
     * Если ветка не указана объект перемещается в корень.
     * Если предшественник не указан, объект перемещается в начало ветки.
     * @param IHierarchicObject $object перемещаемый объект
     * @param IHierarchicObject|null $branch ветка, в которую будет перемещен объект
     * @param IHierarchicObject|null $previousSibling объект, предшествующий перемещаемому
     * @return self
     */
    public function move(
        IHierarchicObject $object,
        IHierarchicObject $branch = null,
        IHierarchicObject $previousSibling = null
    );

    /**
     * Изменяет последнюю часть ЧПУ у объекта.
     * @param IHierarchicObject $object изменяемый объект
     * @param string $slug последняя часть ЧПУ
     * @return self
     */
    public function changeSlug(IHierarchicObject $object, $slug);

    /**
     * Возвращает селектор для выбора родителей страницы
     * @param IHierarchicObject $object
     * @return ISelector
     */
    public function selectAncestry(IHierarchicObject $object);

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о родителе объекта
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getParentField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о "предках" объекта
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getMPathField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о иерархическом порядке
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getHierarchyOrderField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции
     * для хранения информации об уровне вложенности в иерархии
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getHierarchyLevelField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения информации о количестве детей
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getHierarchyChildCountField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения последней части ЧПУ
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getSlugField();

    /**
     * Возвращает поле, которое используется у базового типа коллекции для хранения URI
     * @throws NonexistentEntityException если такого поля не существует
     * @return IField
     */
    public function getURIField();
}

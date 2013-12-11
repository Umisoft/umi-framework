<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\manager;

use umi\orm\collection\ICollection;
use umi\orm\exception\IException;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;

/**
 * Менеджер объектов (Identify Map).
 * @internal
 */
interface IObjectManager
{

    /**
     * Возвращает загруженный ранее объект коллекции по его идентификатору, либо NULL
     * @param ICollection $collection коллекция
     * @param string|integer $objectId уникальный идентификатор объекта
     * @return IObject|null если объект не найден
     */
    public function getObjectInstanceById(ICollection $collection, $objectId);

    /**
     * Возвращает загруженный ранее объект коллекции по его GUID, либо NULL
     * @param string $guid уникальный GUID объекта
     * @return IObject|null если объект не найден
     */
    public function getObjectInstanceByGuid($guid);

    /**
     * Создает и регистрирует новый экземпляр объекта
     * @param ICollection $collection коллекция объектов
     * @param IObjectType $objectType тип объекта
     * @throws IException если не удалось создать объект
     * @return IObject
     */
    public function registerNewObject(ICollection $collection, IObjectType $objectType);

    /**
     * Создает и регистрирует экземпляр существующего объекта, загруженного
     * @param ICollection $collection коллекция объектов
     * @param IObjectType $objectType тип объекта
     * @param string|integer $objectId уникальный идентификатор объекта
     * @param string $guid уникальный GUID объекта
     * @return IObject
     */
    public function registerLoadedObject(ICollection $collection, IObjectType $objectType, $objectId, $guid);

    /**
     * Восстанавливает экземпляр объекта в менеджере
     * @param IObject $object
     * @return self
     */
    public function wakeUpObject(IObject $object);

    /**
     * Выгружает все объекты из памяти. <br />
     * Все изменения, которые были не применены будут утеряны.
     * @return self
     */
    public function unloadObjects();

    /**
     * Помещает новый объект в хранилище объектов и помечает его как неновый
     * @param IObject $object
     * @return IObject
     */
    public function storeNewObject(IObject $object);

    /**
     * Выгружает объект.
     * @param IObject $object
     * @return self
     */
    public function unloadObject(IObject $object);

    /**
     * Возвращает все зарегестрированные объекты
     * @return IObject[]
     */
    public function getObjects();
}

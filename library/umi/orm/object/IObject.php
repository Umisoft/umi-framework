<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object;

use ArrayAccess;
use Serializable;
use umi\orm\collection\IHierarchicCollection;
use umi\orm\collection\ILinkedHierarchicCollection;
use umi\orm\collection\ISimpleCollection;
use umi\orm\exception\IException;
use umi\orm\exception\LoadEntityException;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\ReadOnlyEntityException;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\IObjectType;
use umi\orm\object\property\IProperty;

/**
 * Объект данных.
 * Объект данных - это объект бизнес-логики приложения (domain object),
 * принадлежащий одной из коллекций модели данных.
 * Например: заказ, пользователь, баннер
 * Объект может иметь не ограниченное количество свойств, задаваемых его типом.
 */
interface IObject extends ArrayAccess, Serializable
{
    /**
     * Имя поля, используемого в качестве первичного ключа
     */
    const FIELD_IDENTIFY = 'id';
    /**
     * Имя поля, используемого для хранения уникального глобального идентификатора объекта
     */
    const FIELD_GUID = 'guid';
    /**
     * Имя поля, используемого для хранения информации о типе объекта
     */
    const FIELD_TYPE = 'type';
    /**
     * Имя поля, используемого для хранения информации о версии объекта
     */
    const FIELD_VERSION = 'version';

    /**
     * Префикс для методов-валидаторов конкретных свойств
     */
    const VALIDATOR_METHOD_PREFIX = 'validate';

    /**
     * Инициализирует объект с указанными значениями внутренних свойств из БД
     * @internal
     * @param array $initialValues внутренние значения свойств array(propName => internalValue, ...)
     * @return self
     */
    public function setInitialValues(array $initialValues);

    /**
     * Возвращает внутренние значения свойств объекта, которые уже были загружены из БД
     * @internal
     * @return array $initialValues внутренние значения свойств array(propName => internalValue, ...)
     */
    public function getInitialValues();

    /**
     * Возвращает имя коллекции, к которой принадлежит объект
     * @return string
     */
    public function getCollectionName();

    /**
     * Возвращает коллекцию, к которой принадлежит объект
     * @return ISimpleCollection|IHierarchicCollection|ILinkedHierarchicCollection
     */
    public function getCollection();

    /**
     * Возвращает имя типа объекта
     * @return string
     */
    public function getTypeName();

    /**
     * Возвращает тип объекта
     * @return IObjectType
     */
    public function getType();

    /**
     * Возвращает путь к типу объекта
     * @return string
     */
    public function getTypePath();

    /**
     * Возвращает идентификатор объекта
     * @return string|integer|null null, если объект новый
     */
    public function getId();

    /**
     * Возвращает глобальный уникальный идентификатор объекта
     * @return string
     */
    public function getGUID();

    /**
     * Устанавливает глобальный уникальный идентификатор объекта.
     * @param string $guid
     * @return self
     */
    public function setGUID($guid);

    /**
     * Возвращает версию объекта
     * @return int
     */
    public function getVersion();

    /**
     * Устанавливает версию объекта.
     * Используется для контроля целостности.
     * @param int $version
     * @return self
     */
    public function setVersion($version);

    /**
     * Возвращает список свойств, которые были загружены
     * @return IProperty[]
     */
    public function getProperties();

    /**
     * Возвращает список модифицированных свойств объекта
     * @return IProperty[]
     */
    public function getModifiedProperties();

    /**
     * Загружает и возвращает список всех возможных свойств объекта,
     * включая локализованные
     * @return IProperty[]
     */
    public function getAllProperties();

    /**
     * Проверяет, существует ли свойство
     * @param string $propName имя свойства
     * @param null|string $localeId идентификатор локали свойства
     * @return bool
     */
    public function hasProperty($propName, $localeId = null);

    /**
     * Возвращает свойство объекта
     * @param string $propName имя свойства
     * @param null|string $localeId идентификатор локали свойства
     * @throws RuntimeException если объект уже выгружен из менеджера объектов
     * @throws NonexistentEntityException если свойства с указанным именем, либо локалью не существует
     * @return IProperty
     */
    public function getProperty($propName, $localeId = null);

    /**
     * Возвращает значение свойства
     * @param string $propName имя свойства
     * @param null|string $localeId идентификатор локали свойства.
     * Если не указан (null), будет возвращено вычисленное значение для текущей локали с учетом дефолтной локали:
     * Например, если $localeId не указан, есть поле title, текущая локаль en, а дефолтная ru,
     * то если title#en = null (то есть не переведен) будет возвращен title#ru
     * @return mixed|null значение свойства, либо null, если свойство не существует либо не установлено
     */
    public function getValue($propName, $localeId = null);

    /**
     * Устанавливает в качестве значения свойства значение по умолчанию
     * @param string $propName имя свойства
     * @param null|string $localeId идентификатор локали свойства. Если null, будет установлено значение в текущей локали
     * @return self
     */
    public function setDefaultValue($propName, $localeId = null);

    /**
     * Устанавливает значение свойства
     * @param string $propName имя свойства
     * @param mixed $value значение свойства
     * @param null|string $localeId идентификатор локали свойства.
     * Если не указан (null), будет установлено значение для текущей локали
     * @throws IException если не удалось получить свойство
     * @throws ReadOnlyEntityException если свойство доступно только на чтение
     * @return self
     */
    public function setValue($propName, $value, $localeId = null);

    /**
     * Проверяет, новый ли объект
     * @internal
     * @return bool
     */
    public function getIsNew();

    /**
     * Проверяет, выгружен ли объект из менеджера объектов
     * @internal
     * @return bool
     */
    public function getIsUnloaded();

    /**
     * Устанавливает флаг "новый". <br />
     * @internal
     * @param bool $new
     * @return self
     */
    public function setIsNew($new = true);

    /**
     * Проверяет, модифицирован ли объект
     * @internal
     * @return bool
     */
    public function getIsModified();

    /**
     * Устанавливает флаг "объект модифицирован".
     * @internal
     * @return self
     */
    public function setIsModified();

    /**
     * Помечает объект, как консистентный с БД
     * @internal
     * @return self
     */
    public function setIsConsistent();

    /**
     * Откатывает состояние объекта. Вызывает rollback у всех загруженных свойств объекта. <br />
     * Помечает объкект как не модифицированный.
     * @return self
     */
    public function rollback();

    /**
     * Производит валидацию модифицированного объекта
     * @return bool результат валидации
     */
    public function validate();

    /**
     * Догружает объект из базы полностью
     * @internal
     * @param bool $withLocalization загружать ли все локализованные поля
     * @throws LoadEntityException если у загружаемого объекта нет идентификатора
     */
    public function fullyLoad($withLocalization = false);

    /**
     * Возвращает список ошибок валидации объекта
     * @return array массив ошибок в формате array('propertyName' => array('Error string', ...), ...))
     */
    public function getValidationErrors();

    /**
     * Очищает список ошибок, вызванных валидацией объекта
     * @return array
     */
    public function clearValidationErrors();

    /**
     * Добавляет ошибку валидации объекта
     * @internal
     * @param string $propertyName имя не валидного свойства
     * @param array $errors ошибки
     * @return self
     */
    public function addValidationError($propertyName, array $errors);

    /**
     * Выгружает объект из менеджера объектов.
     * Дальнейшие действия с объектом не возможны
     * @return self
     */
    public function unload();

    /**
     * Сбрасывает значения свойств объекта.
     * Если свойства уже были загружены, то при обращении к объекту они будут загружены еще раз.
     * @return self
     */
    public function reset();

    /**
     * Магический setter ($object->propName = $someValue)
     * @internal
     * @param string $propName имя свойства
     * @param mixed $value значение свойства
     */
    public function __set($propName, $value);

    /**
     * Магический getter ($object->propName)
     * @internal
     * @param string $propName имя свойства
     * @return mixed
     */
    public function __get($propName);

    /**
     * Магический isset. Проверяет наличие свойства у объекта (isset($object->propName))
     * @internal
     * @param string $propName имя свойства
     * @return bool
     */
    public function __isset($propName);

    /**
     * Магический unset. Сбрасывает значение свойства в дефолтное. (unset($object->propName))
     * @param string $propName имя свойства
     */
    public function __unset($propName);

}

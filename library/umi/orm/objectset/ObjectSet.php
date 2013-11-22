<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

use SplObjectStorage;
use umi\dbal\builder\IQueryResult;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\LoadEntityException;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;
use umi\orm\selector\ISelector;

/**
 * Набор объектов коллекции.
 */
class ObjectSet implements IObjectSet, ILocalizable, ICollectionManagerAware
{

    use TLocalizable;
    use TCollectionManagerAware;

    protected $fetchedResults;

    /**
     * @var IQueryResult $queryResult результат запроса
     */
    protected $queryResult;
    /**
     * @var bool $isCompletelyLoaded флаг "все объекты набора полностью загружены"
     */
    protected $isCompletelyLoaded = false;
    /**
     * @var SplObjectStorage $set из загруженных IObject
     */
    protected $set;
    /**
     * @var array $iteratorArray итерируемый массив
     */
    protected $iteratorArray = [];

    /**
     * @var ISelector $selector селектор объектов
     */
    private $selector;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->set = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function setSelector(ISelector $selector)
    {
        if ($this->selector) {
            throw new RuntimeException($this->translate(
                'Selector for object set already injected.'
            ));
        }
        $this->selector = $selector;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelector()
    {
        if (!$this->selector) {
            throw new RuntimeException($this->translate(
                'Selector for object set is not injected.'
            ));
        }

        return $this->selector;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->set = new SplObjectStorage();
        $this->queryResult = null;
        $this->fetchedResults = null;
        $this->isCompletelyLoaded = false;
        $this->iteratorArray = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll()
    {
        $result = [];
        foreach ($this as $object) {
            $result[] = $object;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch()
    {
        $currentObject = null;
        if ($this->valid()) {
            $currentObject = current($this->iteratorArray);
            $this->next();
        }

        return $currentObject;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->iteratorArray);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->iteratorArray);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->iteratorArray);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if (null != ($object = current($this->iteratorArray))) {
            return true;
        }
        if ($this->isCompletelyLoaded) {
            return false;
        }
        if (null != ($row = $this->getQueryResultRow())) {
            $object = $this->loadObjects($row);
            $this->set->attach($object);
            $this->iteratorArray[] = $object;

            return true;
        }
        $this->isCompletelyLoaded = true;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->iteratorArray);
    }

    /**
     * Возвращает количество объектов наборе
     * @return int кол-во загруженных объектов в наборе
     */
    public function count()
    {
        return $this->getQueryResult()
            ->countRows();
    }

    /**
     * Запускает селектор и возвращает результат
     * @return IQueryResult
     */
    protected function getQueryResult()
    {
        if ($this->queryResult) {
            return $this->queryResult;
        }

        return $this->queryResult = $this->getSelector()
            ->getSelectBuilder()
            ->execute();
    }

    /**
     * Возвращает информацию о следующей строке результата.
     */
    protected function getQueryResultRow()
    {
        if ($this->fetchedResults === null) {
            $this->fetchedResults = $this
                ->getQueryResult()
                ->fetchAll();
        }

        return array_shift($this->fetchedResults);
    }

    /**
     * Загружает информацию об объекте и связанных с ним объектов из строки БД
     * @param array $row строка из бд в определенном формате
     * @throws LoadEntityException если не удалось загрузить объекты
     * @return IObject основной объект
     */
    protected function loadObjects(array $row)
    {

        $objectsInfo = [];
        foreach ($row as $alias => $value) {
            $pos = strrpos($alias, ISelector::ALIAS_SEPARATOR);
            if (!$pos) {
                throw new LoadEntityException($this->translate(
                    'Cannot load objects from data row. Field alias "{alias}" is not correct.',
                    ['alias' => $alias]
                ));
            }
            $collectionPath = substr($alias, 0, $pos);
            $fieldName = substr($alias, $pos + 1);

            if (!isset($objectsInfo[$collectionPath])) {
                $objectsInfo[$collectionPath] = [];
            }
            $objectsInfo[$collectionPath][$fieldName] = $value;
        }

        $mainObject = null;
        foreach ($objectsInfo as $collectionPath => $objectInfo) {

            if (!array_key_exists(IObject::FIELD_TYPE, $objectInfo)) {
                throw new LoadEntityException($this->translate(
                    'Cannot load object from data row. Information about object type is not found.'
                ));
            }

            if (!is_null($objectInfo[IObject::FIELD_TYPE])) {
                $object = $this->loadObject($objectInfo[IObject::FIELD_TYPE], $objectInfo);
                if (!$mainObject && !substr_count($collectionPath, ISelector::FIELD_SEPARATOR)) {
                    $mainObject = $object;
                }
            }

        }
        if (!$mainObject) {
            throw new LoadEntityException($this->translate(
                'Cannot detect main object from data row.'
            ));
        }

        return $mainObject;
    }

    /**
     * Загружает объект из массива данных.
     * @param string $objectTypePath информация о типе объекта
     * @param array $objectInfo данные
     * @throws LoadEntityException если не удалось загрузить объект
     * @return IObject
     */
    protected function loadObject($objectTypePath, array $objectInfo)
    {

        $objectTypeInfo = explode(IObjectType::PATH_SEPARATOR, $objectTypePath, 2);
        if (count($objectTypeInfo) < 2) {
            throw new LoadEntityException($this->translate(
                'Cannot load object from data row. Object type path "{path}" is not correct.',
                ['path' => $objectTypePath]
            ));
        }

        list($objectCollectionName, $objectTypeName) = $objectTypeInfo;
        $objectCollection = $this->getCollectionManager()
            ->getCollection($objectCollectionName);
        $objectType = $objectCollection->getMetadata()
            ->getType($objectTypeName);

        $object = $objectCollection->loadObject($objectType, $objectInfo);

        return $object;

    }

}

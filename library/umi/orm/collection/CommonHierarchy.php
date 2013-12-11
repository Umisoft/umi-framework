<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use Doctrine\DBAL\Connection;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\field\special\MaterializedPathField;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;

/**
 * Общая иерархия.
 */
class CommonHierarchy extends BaseHierarchicCollection implements ICommonHierarchy, ICollectionManagerAware
{

    use TCollectionManagerAware;

    /**
     * {@inheritdoc}
     */
    public function contains(IObject $object)
    {
        if (parent::contains($object)) {
            return true;
        }

        if ($object->getCollection() instanceof ILinkedHierarchicCollection) {
            return $object
                ->getCollection()
                ->getCommonHierarchy() === $this;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function add(
        ILinkedHierarchicCollection $linkedCollection,
        $slug,
        $typeName = IObjectType::BASE,
        IHierarchicObject $branch = null
    )
    {
        return $linkedCollection->add($slug, $typeName, $branch);
    }

    /**
     * Запускает транзакцию по перемещению объекта.
     * @param IHierarchicObject $object перемещаемый объект
     * @param IHierarchicObject|null $branch ветка, в которую будет перемещен объект
     * @param IHierarchicObject|null $previousSibling объект, предшествующий перемещаемому
     * @throws RuntimeException если не удалось переместить объекты
     * @return self
     */
    protected function persistMovedObjects(
        IHierarchicObject $object,
        IHierarchicObject $branch = null,
        IHierarchicObject $previousSibling = null
    )
    {

        $order = $previousSibling ? $previousSibling->getOrder() + 1 : 1;

        /**
         * @var IUpdateBuilder[] $builders
         */
        $builders = [];
        $builders[] = $this->buildUpdateOrderQueryForMovedObject($object, $this, $order);
        $builders[] = $this->buildUpdateOrderQueryForMovedObject($object, $object->getCollection(), $order);
        $collections = $orderCollections = $this->getCollectionsForChangingOrder($branch);
        foreach ($collections as $collection) {
            $builders[] = $this->buildUpdateOrderQueryForSiblings($object, $collection, $order, $branch);
        }

        if ($object->getParent() !== $branch) {

            if (null != ($parent = $object->getParent())) {
                $builders[] = $this->buildUpdateChildCountQuery($parent, $this, -1);
                $builders[] = $this->buildUpdateChildCountQuery($parent, $parent->getCollection(), -1);
            }
            if ($branch) {
                $builders[] = $this->buildUpdateChildCountQuery($branch, $this, 1);
                $builders[] = $this->buildUpdateChildCountQuery($branch, $branch->getCollection(), 1);
            }
            $builders[] = $this->buildUpdateHierarchicPropertiesQueryForMovedObject($object, $this, $branch);
            $builders[] = $this->buildUpdateHierarchicPropertiesQueryForMovedObject(
                $object,
                $object->getCollection(),
                $branch
            );

            $childrenCollections = $this->getMovedObjectChildrenCollections($object);

            foreach ($childrenCollections as $collection) {
                $builders[] = $this->buildUpdateHierarchicPropertiesQueryForMovedObjectChildren(
                    $object,
                    $collection,
                    $branch
                );
            }
            $collections = array_merge($collections, $childrenCollections);
        }

        $drivers = $this->detectUsedDriversByCollections($collections);

        $this
            ->getObjectPersister()
            ->executeTransaction(
                function () use ($builders, $object, $branch) {
                    $this->checkIfMovePossible($object, $this, $branch);
                    foreach ($builders as $builder) {
                        $builder->execute();
                    }
                },
                $drivers
            );
    }

    /**
     * Запускает транзакцию по изменению последней части ЧПУ объекта.
     * @param IHierarchicObject $object изменяемый объект
     * @param $newSlug $slug последняя часть ЧПУ
     * @throws RuntimeException если невозможно изменить объект
     */
    protected function persistChangedSlug(IHierarchicObject $object, $newSlug)
    {
        $commonConnection = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->getConnection();

        // start common root transaction and pass used connection inside it
        $commonConnection->transactional(
            function () use ($object, $newSlug) {
                $collections = $orderCollections = $this->getCollectionsForChangingSlug($object);
                $drivers = $this->detectUsedDriversByCollections($collections);
                /**
                 * @var IUpdateBuilder[] $builders
                 */
                $builders = [];
                foreach ($collections as $collection) {
                    $builders[] = $this->buildUpdateUrlQuery($object, $collection, $newSlug);
                }
                $this
                    ->getObjectPersister()
                    ->executeTransaction(
                        function () use ($builders, $object, $newSlug) {

                            $this->checkIfChangeSlugPossible($object, $this, $newSlug);
                            foreach ($builders as $builder) {
                                $builder->execute();
                            }

                        },
                        $drivers
                    );
            }
        );

    }

    /**
     * Возвращает список имен коллекций, которые будут затронуты при изменении родителя объекта.
     * @param IHierarchicObject $object объект, родитель которого изменяется
     * @return IHierarchicCollection[]
     */
    protected function getMovedObjectChildrenCollections(IHierarchicObject $object)
    {
        $typeColumnName = $this
            ->getObjectTypeField()
            ->getColumnName();
        /**
         * @var ISelectBuilder $selectBuilder
         */
        $selectBuilder = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->select($typeColumnName)
            ->where()
            ->expr(
                $this
                    ->getMPathField()
                    ->getColumnName(),
                'like',
                ':' . $this
                    ->getMPathField()
                    ->getName()
            )
            ->bindValue(
                ':' . $this
                    ->getMPathField()
                    ->getName(),
                $object->getMaterializedPath() . MaterializedPathField::MPATH_SEPARATOR . '%',
                $this
                    ->getMPathField()
                    ->getDataType()
            );

        $selectBuilder->groupBy($typeColumnName);
        $result = $selectBuilder->execute();

        $collections = [];

        while (null != ($row = $result->fetch())) {
            $typeInfo = $row[$typeColumnName];
            if (0 != ($pos = strpos($typeInfo, IObjectType::PATH_SEPARATOR))) {
                $collectionName = substr($typeInfo, 0, $pos);
                $collections[$collectionName] = $this
                    ->getCollectionManager()
                    ->getCollection($collectionName);
            }
        }

        if (count($collections)) {
            $collections[$this->getName()] = $this;
        }

        return $collections;
    }

    /**
     * Возвращает список имен коллекций, которые будут затронуты при изменении порядка в иерархии.
     * @param IHierarchicObject|null $branch ветка, в которой происходит изменение, null в случае перемещения в корне
     * @return IHierarchicCollection[]
     */
    protected function getCollectionsForChangingOrder(IHierarchicObject $branch = null)
    {

        $typeColumnName = $this
            ->getObjectTypeField()
            ->getColumnName();
        $parentField = $this->getParentField();

        /**
         * @var $selectBuilder ISelectBuilder
         */
        $selectBuilder = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->select($typeColumnName);
        $selectBuilder->groupBy($typeColumnName);

        if ($branch) {
            $selectBuilder
                ->where()
                ->expr($parentField->getColumnName(), '=', ':' . $parentField->getName())
                ->bindValue(':' . $parentField->getName(), $branch->getId(), $parentField->getDataType());
        } else {
            $selectBuilder
                ->where()
                ->expr($parentField->getColumnName(), 'IS', ':' . $parentField->getName())
                ->bindNull(':' . $parentField->getName());
        }

        $result = $selectBuilder->execute();

        $collections = [];

        while (null != ($row = $result->fetch())) {
            $typeInfo = $row[$typeColumnName];
            if (0 != ($pos = strpos($typeInfo, IObjectType::PATH_SEPARATOR))) {
                $collectionName = substr($typeInfo, 0, $pos);
                $collections[$collectionName] = $this
                    ->getCollectionManager()
                    ->getCollection($collectionName);
            }
        }

        if (count($collections)) {
            $collections[$this->getName()] = $this;
        }

        return $collections;
    }

    /**
     * Возвращает список имен коллекций, которые будут затронуты при изменении последней части ЧПУ объекта
     * @param IHierarchicObject $object изменяемый объект
     * @return IHierarchicCollection[]
     */
    protected function getCollectionsForChangingSlug(IHierarchicObject $object)
    {

        $typeColumnName = $this
            ->getObjectTypeField()
            ->getColumnName();
        $mpathField = $this->getMPathField();

        /**
         * @var $selectBuilder ISelectBuilder
         */
        $selectBuilder = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->select($typeColumnName)
            ->where()
            ->expr($mpathField->getColumnName(), 'like', ':' . $mpathField->getName())
            ->bindValue(
                ':' . $mpathField->getName(),
                $object->getMaterializedPath() . MaterializedPathField::MPATH_SEPARATOR . '%',
                $mpathField->getDataType()
            );
        $selectBuilder->groupBy($typeColumnName);
        $result = $selectBuilder->execute();

        $collections = [];
        $collections[$this->getName()] = $this;
        $collections[$object
            ->getCollection()
            ->getName()] = $object
            ->getCollection()
            ->getName();

        $collections = [
            $this->getName() => $this,
            $object
                ->getCollection()
                ->getName()  => $object->getCollection()
        ];

        while (null != ($row = $result->fetch())) {
            $typeInfo = $row[$typeColumnName];
            if (0 != ($pos = strpos($typeInfo, IObjectType::PATH_SEPARATOR))) {
                $collectionName = substr($typeInfo, 0, $pos);
                $collections[$collectionName] = $this
                    ->getCollectionManager()
                    ->getCollection($collectionName);
            }
        }

        return $collections;
    }

    /**
     * Определяет используемые для коллекций соединения с БД
     * @param IHierarchicCollection[] $collections
     * @return Connection[]
     */
    protected function detectUsedDriversByCollections(array $collections)
    {
        $drivers = [];

        foreach ($collections as $collection) {
            $source = $collection
                ->getMetadata()
                ->getCollectionDataSource();
            $drivers[$source->getMasterServerId()] = $source
                ->getMasterServer()
                ->getConnection();
        }

        return $drivers;
    }
}

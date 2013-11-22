<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\builder\SelectBuilder;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\field\relation\BelongsToRelationField;
use umi\orm\metadata\field\special\MaterializedPathField;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use umi\orm\object\property\ICounterProperty;

/**
 * Базовый класс иерархической коллекции
 */
abstract class BaseHierarchicCollection extends BaseCollection implements IHierarchicCollection
{

    /**
     * {@inheritdoc}
     */
    public function delete(IObject $object)
    {
        parent::delete($object);
        /**
         * @var IHierarchicObject $object
         */
        if (null != ($parent = $object->getParent())) {
            /**
             * @var ICounterProperty $childCount
             */
            $childCount = $parent->getProperty(IHierarchicObject::FIELD_CHILD_COUNT);
            $childCount->decrement();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForcedFieldsToLoad()
    {
        $fieldsToLoad = parent::getForcedFieldsToLoad();
        $fieldsToLoad[IHierarchicObject::FIELD_PARENT] = $this->getRequiredField(IHierarchicObject::FIELD_PARENT);
        $fieldsToLoad[IHierarchicObject::FIELD_MPATH] = $this->getRequiredField(IHierarchicObject::FIELD_MPATH);
        $fieldsToLoad[IHierarchicObject::FIELD_SLUG] = $this->getRequiredField(IHierarchicObject::FIELD_SLUG);
        $fieldsToLoad[IHierarchicObject::FIELD_URI] = $this->getRequiredField(IHierarchicObject::FIELD_URI);

        return $fieldsToLoad;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_PARENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getMPathField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_MPATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyOrderField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyLevelField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_HIERARCHY_LEVEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyChildCountField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_CHILD_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_SLUG);
    }

    /**
     * {@inheritdoc}
     */
    public function getURIField()
    {
        return $this->getRequiredField(IHierarchicObject::FIELD_URI);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxOrder(IHierarchicObject $branch = null)
    {
        if ($branch && !$this->contains($branch)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot calculate max order. Branch from another collection given.'
            ));
        }

        $dataSource = $this
            ->getMetadata()
            ->getCollectionDataSource();
        $orderField = $this->getHierarchyOrderField();
        $parentField = $this->getParentField();

        /**
         * @var ISelectBuilder $select
         */
        $select = $dataSource
            ->select(':' . $orderField->getName() . ' as ' . $orderField->getName())
            ->bindExpression(':' . $orderField->getName(), 'MAX(`' . $orderField->getColumnName() . '`)');

        if ($branch) {
            $select
                ->where()
                ->expr($parentField->getColumnName(), '=', ':' . $parentField->getName())
                ->bindValue(':' . $parentField->getName(), $branch->getId(), $parentField->getDataType());
        } else {
            $select
                ->where()
                ->expr($parentField->getColumnName(), 'IS', ':' . $parentField->getName())
                ->bindNull(':' . $parentField->getName());
        }

        return (int) $select
            ->execute()
            ->fetchColumn();

    }

    /**
     * {@inheritdoc}
     */
    public function move(
        IHierarchicObject $object,
        IHierarchicObject $branch = null,
        IHierarchicObject $previousSibling = null
    )
    {

        if (!$this
            ->getObjectPersister()
            ->getIsPersisted()
        ) {
            throw new RuntimeException($this->translate(
                'Cannot move object. Not all objects are persisted. Commit transaction first.'
            ));
        }

        if (!$this->contains($object)) {
            throw new RuntimeException($this->translate(
                'Cannot move object from another hierarchy.'
            ));
        }

        if ($branch) {
            if (!$this->contains($branch)) {
                throw new RuntimeException($this->translate(
                    'Cannot move object to branch from another hierarchy.'
                ));
            }

            /**
             * @var BelongsToRelationField $parentField
             */
            $parentField = $object
                ->getProperty(IHierarchicObject::FIELD_PARENT)
                ->getField();
            $parentTargetCollectionName = $parentField->getTargetCollectionName();

            if ($this->getName() != $parentTargetCollectionName && $branch->getCollectionName(
                ) != $parentTargetCollectionName
            ) {
                throw new RuntimeException($this->translate(
                    'Cannot move object. Branch collection does not match object parent collection.'
                ));
            }

            $objectMpath = $object->getMaterializedPath();

            if (strpos($branch->getMaterializedPath(), $objectMpath) === 0) {
                throw new RuntimeException($this->translate(
                    'Cannot move parent object under its child.'
                ));
            }
        }

        if ($previousSibling) {
            if (!$this->contains($previousSibling)) {
                throw new RuntimeException($this->translate(
                    'Cannot move object after sibling from another hierarchy.'
                ));
            }

            if ($previousSibling->getParent() !== $branch) {
                throw new RuntimeException($this->translate(
                    'Cannot move object. Sibling should be direct child of the given parent.'
                ));
            }
        }

        $this->persistMovedObjects($object, $branch, $previousSibling);
        $this->resetObjects();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function changeSlug(IHierarchicObject $object, $slug)
    {

        if (!$this
            ->getObjectPersister()
            ->getIsPersisted()
        ) {
            throw new RuntimeException($this->translate(
                'Cannot change object slug. Not all objects are persisted. Commit transaction first.'
            ));
        }

        $this->persistChangedSlug($object, $slug);
        $this->resetObjects();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function selectAncestry(IHierarchicObject $object)
    {

        $mpath = substr($object->getMaterializedPath(), strlen(MaterializedPathField::MPATH_START_SYMBOL));

        $ids = explode(MaterializedPathField::MPATH_SEPARATOR, $mpath);
        if (count($ids) < 2) {
            return $this->emptySelect();
        }
        array_pop($ids);

        $selector = $this->select();
        $selector
            ->where(IHierarchicObject::FIELD_IDENTIFY)
            ->in($ids);

        return $selector;
    }

    /**
     * Сбрасывает состояние объектов коллекции для повторной загрузки актуальной информации.
     * Используется при модификации данных прямыми запросами.
     */
    protected function resetObjects()
    {
        $objects = $this
            ->getObjectManager()
            ->getObjects();
        foreach ($objects as $object) {
            if ($this->contains($object)) {
                $object->reset();
            }
        }
    }

    /**
     * Запускает транзакцию по перемещению объекта.
     * @param IHierarchicObject $object перемещаемый объект
     * @param IHierarchicObject|null $branch ветка, в которую будет перемещен объект
     * @param IHierarchicObject|null $previousSibling объект, предшествующий перемещаемому
     * @throws RuntimeException если не удалось переместить объекты
     */
    protected function persistMovedObjects(
        IHierarchicObject $object,
        IHierarchicObject $branch = null,
        IHierarchicObject $previousSibling = null
    )
    {
        $affectedDriver = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->getMasterServer()
            ->getConnection();

        $this
            ->getObjectPersister()
            ->executeTransaction(
                function () use ($object, $branch, $previousSibling) {

                    $this->checkIfMovePossible($object, $this, $branch);

                    $order = $previousSibling ? $previousSibling->getOrder() + 1 : 1;

                    /**
                     * @var IUpdateBuilder[] $builders
                     */
                    $builders = [
                        $this->buildUpdateOrderQueryForMovedObject($object, $this, $order),
                        $this->buildUpdateOrderQueryForSiblings($object, $this, $order, $branch)
                    ];

                    if ($object->getParent() !== $branch) {
                        if (null != ($parent = $object->getParent())) {
                            $builders[] = $this->buildUpdateChildCountQuery($parent, $this, -1);
                        }
                        if ($branch) {
                            $builders[] = $this->buildUpdateChildCountQuery($branch, $this, 1);
                        }
                        $builders[] = $this->buildUpdateHierarchicPropertiesQueryForMovedObject(
                            $object,
                            $this,
                            $branch
                        );
                        $builders[] = $this->buildUpdateHierarchicPropertiesQueryForMovedObjectChildren(
                            $object,
                            $this,
                            $branch
                        );
                    }

                    foreach ($builders as $builder) {
                        $builder->execute();
                    }

                },
                [$affectedDriver]
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

        $affectedDriver = $this
            ->getMetadata()
            ->getCollectionDataSource()
            ->getMasterServer()
            ->getConnection();

        $this
            ->getObjectPersister()
            ->executeTransaction(
                function () use ($object, $newSlug) {

                    $this->checkIfChangeSlugPossible($object, $this, $newSlug);
                    $this
                        ->buildUpdateUrlQuery($object, $this, $newSlug)
                        ->execute();

                },
                [$affectedDriver]
            );
    }

    /**
     * Возвращает запрос на изменения родителя, mpath, url и уровня вложенности перемещаемого объекта.
     * @param IHierarchicObject $object объект-инициатор перемещения
     * @param IHierarchicCollection $collection коллекция, для которой формируется запрос
     * @param IHierarchicObject|null $newParent новый родитель
     * @return IUpdateBuilder
     */
    protected function buildUpdateHierarchicPropertiesQueryForMovedObject(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        IHierarchicObject $newParent = null
    )
    {

        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();
        $idField = $collection->getIdentifyField();
        $parentField = $collection->getParentField();
        $versionField = $collection->getVersionField();
        $mpathField = $collection->getMPathField();
        $levelField = $collection->getHierarchyLevelField();
        $urlField = $collection->getURIField();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();

        $newParentUrl = $newParent ? $newParent->getURI() . '/' : '//';
        $update
            ->set($urlField->getColumnName())
            ->bindValue(':' . $urlField->getColumnName(), $newParentUrl . $object->getSlug(), $urlField->getDataType());

        $newMpathStart = $newParent ? $newParent->getMaterializedPath(
            ) . MaterializedPathField::MPATH_SEPARATOR : MaterializedPathField::MPATH_START_SYMBOL;
        $update
            ->set($mpathField->getColumnName())
            ->bindValue(
                ':' . $mpathField->getColumnName(),
                $newMpathStart . $object->getId(),
                $mpathField->getDataType()
            );

        if ($newParent) {
            $update
                ->set($parentField->getColumnName())
                ->bindValue(':' . $parentField->getColumnName(), $newParent->getId(), $parentField->getDataType());
        } else {
            $update
                ->set($parentField->getColumnName())
                ->bindNull(':' . $parentField->getColumnName());
        }

        $branchLevel = $newParent ? $newParent->getLevel() : -1;
        $levelDelta = $branchLevel - $object->getLevel() + 1;
        if ($levelDelta) {
            $changingLevelExpression = $update
                    ->getConnection()
                    ->quoteIdentifier($levelField->getColumnName()) . ' + (' . $levelDelta . ')';
            $update
                ->set($levelField->getColumnName())
                ->bindExpression(':' . $levelField->getColumnName(), $changingLevelExpression);
        }

        $incrementVersionExpression = $update
                ->getConnection()
                ->quoteIdentifier($versionField->getColumnName()) . ' + 1';
        $update
            ->set($versionField->getColumnName())
            ->bindExpression(':' . $versionField->getColumnName(), $incrementVersionExpression);

        $update
            ->where()
            ->expr($idField->getColumnName(), '=', ':' . $idField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType());

        return $update;
    }

    /**
     * Возвращает запрос на изменения материализованного пути и уровня вложенности.
     * @param IHierarchicObject $object объект-инициатор перемещения
     * @param IHierarchicCollection $collection коллекция, в которой происходит перемещение
     * @param IHierarchicObject|null $branch ветка, в которую происходит перемещение, null,
     * если перемещение происходит в корень
     * @return IUpdateBuilder
     */
    protected function buildUpdateHierarchicPropertiesQueryForMovedObjectChildren(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        IHierarchicObject $branch = null
    )
    {

        $parent = $object->getParent();

        $versionField = $collection->getVersionField();
        $mpathField = $collection->getMPathField();
        $levelField = $collection->getHierarchyLevelField();
        $urlField = $collection->getURIField();

        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();
        $driver = $update->getConnection();

        $branchLevel = $branch ? $branch->getLevel() : -1;
        $levelDelta = $branchLevel - $object->getLevel() + 1;
        if ($levelDelta) {
            $changingLevelExpression = $driver->quoteIdentifier(
                    $levelField->getColumnName()
                ) . ' + (' . $levelDelta . ')';
            $update
                ->set($levelField->getColumnName())
                ->bindExpression(':' . $levelField->getColumnName(), $changingLevelExpression);
        }

        $incrementVersionExpression = $driver->quoteIdentifier($versionField->getColumnName()) . ' + 1';
        $update
            ->set($versionField->getColumnName())
            ->bindExpression(':' . $versionField->getColumnName(), $incrementVersionExpression);

        $branchMpath = $branch ? $branch->getMaterializedPath(
            ) . MaterializedPathField::MPATH_SEPARATOR : MaterializedPathField::MPATH_START_SYMBOL;
        $objectParentMpath = $parent ? $parent->getMaterializedPath(
            ) . MaterializedPathField::MPATH_SEPARATOR : MaterializedPathField::MPATH_START_SYMBOL;
        $changingMpathExpression = 'REPLACE(' . $driver->quoteIdentifier(
                $mpathField->getColumnName()
            ) . ', ' . $driver->quote($objectParentMpath) . ', ' . $driver->quote($branchMpath) . ')';
        $update
            ->set($mpathField->getColumnName())
            ->bindExpression(':' . $mpathField->getColumnName(), $changingMpathExpression);

        $objectParentUrl = $parent ? $parent->getURI() . '/' : '//';
        $branchUrl = $branch ? $branch->getURI() . '/' : '//';
        $changingUrlExpression = 'REPLACE(' . $driver->quoteIdentifier(
                $urlField->getColumnName()
            ) . ', ' . $driver->quote($objectParentUrl) . ', ' . $driver->quote($branchUrl) . ')';
        $update
            ->set($urlField->getColumnName())
            ->bindExpression(':' . $urlField->getColumnName(), $changingUrlExpression);

        $update
            ->where()
            ->expr($mpathField->getColumnName(), 'like', ':m_path')
            ->bindValue(
                ':m_path',
                $object->getMaterializedPath() . MaterializedPathField::MPATH_SEPARATOR . '%',
                $mpathField->getDataType()
            );

        return $update;

    }

    /**
     * Возвращает запрос на изменения количества детей.
     * @param IHierarchicObject $object объект, у которого меняется количество детей
     * @param IHierarchicCollection $collection коллекция, для которой формируется запрос
     * @param int $childCountModifier число, на которое увеличивается или уменьшается количество детей
     * @return IUpdateBuilder
     */
    protected function buildUpdateChildCountQuery(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        $childCountModifier
    )
    {

        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();
        $childCountField = $collection->getHierarchyChildCountField();
        $idField = $collection->getIdentifyField();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();

        $modifierExpression = $update
                ->getConnection()
                ->quoteIdentifier($childCountField->getColumnName()) . ' + (' . $childCountModifier . ')';
        $update
            ->set($childCountField->getColumnName())
            ->bindExpression(':' . $childCountField->getColumnName(), $modifierExpression);

        $update
            ->where()
            ->expr($idField->getColumnName(), '=', ':' . $idField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType());

        return $update;

    }

    /**
     * Возвращает запрос на изменение порядка в иерархии перемещаемого объекта
     * @param IHierarchicObject $object перемещаемый объект
     * @param IHierarchicCollection $collection коллекция, для которой строится запрос
     * @param int $order новый порядок объекта
     * @return IUpdateBuilder
     */
    protected function buildUpdateOrderQueryForMovedObject(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        $order
    )
    {

        $orderField = $collection->getHierarchyOrderField();
        $idField = $collection->getIdentifyField();
        $versionField = $collection->getVersionField();
        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();

        $incrementVersionExpression = $update
                ->getConnection()
                ->quoteIdentifier($versionField->getColumnName()) . ' + 1';

        $update
            ->set($orderField->getColumnName())
            ->bindValue(':' . $orderField->getColumnName(), $order, $orderField->getDataType());

        $update
            ->set($versionField->getColumnName())
            ->bindExpression(':' . $versionField->getColumnName(), $incrementVersionExpression);

        $update
            ->where()
            ->expr($idField->getColumnName(), '=', ':' . $idField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType());

        return $update;
    }

    /**
     * Возвращает список запросов на изменения порядка в структуре при перемещении объекта.
     * @param IHierarchicObject $object объект-инициатор перемещения
     * @param IHierarchicCollection $collection коллекция, для которой изменяется порядок
     * @param int $order новая позиция объекта в иерархии
     * @param IHierarchicObject|null $branch ветка в которой происходит перемещение
     * @return IUpdateBuilder
     */
    protected function buildUpdateOrderQueryForSiblings(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        $order,
        IHierarchicObject $branch = null
    )
    {
        $orderField = $collection->getHierarchyOrderField();
        $idField = $collection->getIdentifyField();
        $parentField = $collection->getParentField();
        $versionField = $collection->getVersionField();

        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();

        $incrementOrderExpression = $update
                ->getConnection()
                ->quoteIdentifier($orderField->getColumnName()) . ' + 1';
        $incrementVersionExpression = $update
                ->getConnection()
                ->quoteIdentifier($versionField->getColumnName()) . ' + 1';

        $update
            ->set($orderField->getColumnName())
            ->bindExpression(':' . $orderField->getColumnName(), $incrementOrderExpression);

        $update
            ->set($versionField->getColumnName())
            ->bindExpression(':' . $versionField->getColumnName(), $incrementVersionExpression);

        $update
            ->where()
            ->expr($idField->getColumnName(), '!=', ':' . $idField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType());

        if ($branch) {
            $update
                ->where()
                ->expr($parentField->getColumnName(), '=', ':' . $parentField->getName())
                ->bindValue(':' . $parentField->getName(), $branch->getId(), $parentField->getDataType());
        } else {
            $update
                ->where()
                ->expr($parentField->getColumnName(), 'IS', ':' . $parentField->getName())
                ->bindNull(':' . $parentField->getName());
        }

        $update
            ->where()
            ->expr($orderField->getColumnName(), '>=', ':max' . $orderField->getName())
            ->bindValue(':max' . $orderField->getName(), $order, $orderField->getDataType());

        return $update;
    }

    /**
     * Возвращает запрос на изменения url.
     * @param IHierarchicObject $object объект-инициатор изменения url'ов
     * @param IHierarchicCollection $collection коллекция, для которой строится запрос
     * @param string $newSlug новая последняя часть ЧПУ изменяемого объекта
     * @return IUpdateBuilder
     */
    protected function buildUpdateUrlQuery(IHierarchicObject $object, IHierarchicCollection $collection, $newSlug)
    {

        $objectUrl = $object->getURI();
        $parent = $object->getParent();
        $newUrl = $parent ? $parent->getURI() . '/' . $newSlug : '//' . $newSlug;

        $versionField = $collection->getVersionField();
        $urlField = $collection->getURIField();
        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();

        /**
         * @var $update IUpdateBuilder
         */
        $update = $dataSource->update();

        $driver = $update->getConnection();

        $changingUrlExpression = 'REPLACE(' . $driver->quoteIdentifier(
                $urlField->getColumnName()
            ) . ', ' . $driver->quote($objectUrl) . ', ' . $driver->quote($newUrl) . ')';
        $incrementVersionExpression = $driver->quoteIdentifier($versionField->getColumnName()) . ' + 1';

        $update
            ->set($versionField->getColumnName())
            ->bindExpression(':' . $versionField->getColumnName(), $incrementVersionExpression);

        $update
            ->set($urlField->getColumnName())
            ->bindExpression(':' . $urlField->getColumnName(), $changingUrlExpression);

        $childConditionPlaceholder = ':child_' . $urlField->getName();
        $sameObjectConditionPlaceholder = ':same_' . $urlField->getName();

        $update
            ->where(IExpressionGroup::MODE_OR)
            ->expr($urlField->getColumnName(), 'like', $childConditionPlaceholder)
            ->expr($urlField->getColumnName(), '=', $sameObjectConditionPlaceholder)
            ->bindValue($childConditionPlaceholder, $objectUrl . '/%', $urlField->getDataType())
            ->bindValue($sameObjectConditionPlaceholder, $objectUrl, $urlField->getDataType());

        return $update;

    }

    /**
     * Проверяет, возможно ли перемещение объекта.
     * @param IHierarchicObject $object перемещаемый объект
     * @param IHierarchicCollection $collection коллекция в которой происходит перемещение
     * @param IHierarchicObject|null $branch ветка, в которую пермещается объект
     * @throws RuntimeException если перемещение не возможно
     * @return $this
     */
    protected function checkIfMovePossible(
        IHierarchicObject $object,
        IHierarchicCollection $collection,
        IHierarchicObject $branch = null
    )
    {

        $versionField = $collection->getVersionField();
        $idField = $collection->getIdentifyField();
        $urlField = $collection->getURIField();

        $dataSource = $collection
            ->getMetadata()
            ->getCollectionDataSource();

        $selectBuilder = $dataSource
            ->select($idField->getColumnName())
            ->where()
            ->expr($idField->getColumnName(), '=', ':' . $idField->getName())
            ->expr($versionField->getColumnName(), '=', ':' . $versionField->getName());

        $selectBuilder
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType())
            ->bindValue(':' . $versionField->getName(), $object->getVersion(), $versionField->getDataType())
            ->execute();

        $objectFound = $selectBuilder->getTotal();

        if ($objectFound != 1) {
            throw new RuntimeException($this->translate(
                'Cannot move object with id "{id}". Object is out of date.',
                ['id' => $object->getId()]
            ));
        }

        if ($branch) {
             $selectBuilder
                ->bindValue(':' . $idField->getName(), $branch->getId(), $idField->getDataType())
                ->bindValue(':' . $versionField->getName(), $branch->getVersion(), $versionField->getDataType())
                ->execute();
            $branchFound = $selectBuilder->getTotal();

            if ($branchFound != 1) {
                throw new RuntimeException($this->translate(
                    'Cannot move object with id "{id}" to branch with id "{branchId}". Branch is out of date.',
                    ['id' => $object->getId(), 'branchId' => $branch->getId()]
                ));
            }
        }
        if ($object->getParent() !== $branch) {

            $selectBuilder = $dataSource
                ->select($idField->getColumnName())
                ->where()
                ->expr($urlField->getColumnName(), '=', ':' . $urlField->getName());

            $baseUrl = $branch ? $branch->getURI() . '/' : '//';
            $selectBuilder
                ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType())
                ->bindValue(':' . $urlField->getName(), $baseUrl . $object->getSlug(), $urlField->getDataType())
                ->execute();
            $slugConflict = $selectBuilder->getTotal();
            if ($slugConflict) {
                throw new RuntimeException($this->translate(
                    'Cannot move object with id "{id}". Slug {slug} is not unique.',
                    ['id' => $object->getId(), 'slug' => $object->getSlug()]
                ));
            }
        }

        return $this;
    }

    /**
     * Проверяет, возможно ли изменение последней части ЧПУ у объекта
     * @param IHierarchicObject $object изменяемый объект
     * @param IHierarchicCollection $collection коллекция, для которой выстраена иерархия урлов
     * @param string $newSlug новая последняя часть ЧПУ
     * @throws RuntimeException если при изменении возможны конфликты
     * @return $this
     */
    protected function checkIfChangeSlugPossible(IHierarchicObject $object, IHierarchicCollection $collection, $newSlug)
    {

        $versionField = $collection->getVersionField();
        $idField = $collection->getIdentifyField();
        $urlField = $collection->getURIField();

        /** @var $selectBuilder SelectBuilder */
        $selectBuilder = $collection
            ->getMetadata()
            ->getCollectionDataSource()
            ->select($idField->getColumnName())
            ->where()
            ->expr($idField->getColumnName(), '=', ':' . $idField->getName())
            ->expr($versionField->getColumnName(), '=', ':' . $versionField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType())
            ->bindValue(':' . $versionField->getName(), $object->getVersion(), $versionField->getDataType());

        $objectFound = $selectBuilder->getTotal();

        if ($objectFound != 1) {
            throw new RuntimeException($this->translate(
                'Cannot change slug for object with id "{id}". Object is out of date.',
                ['id' => $object->getId()]
            ));
        }

        $parent = $object->getParent();
        $newUrl = $parent ? $parent->getURI() . '/' . $newSlug : '//' . $newSlug;

        $selectBuilder = $collection
            ->getMetadata()
            ->getCollectionDataSource()
            ->select($idField->getColumnName())
            ->where()
            ->expr($urlField->getColumnName(), '=', ':' . $urlField->getName())
            ->expr($idField->getColumnName(), '!=', ':' . $idField->getName())
            ->bindValue(':' . $idField->getName(), $object->getId(), $idField->getDataType())
            ->bindValue(':' . $urlField->getName(), $newUrl, $urlField->getDataType());

        $selectBuilder->execute();
        $slugConflict = $selectBuilder->getTotal();

        if ($slugConflict) {
            throw new RuntimeException($this->translate(
                'Cannot change slug for object with id "{id}". Slug {slug} is not unique.',
                ['id' => $object->getId(), 'slug' => $newSlug]
            ));
        }

        return $this;
    }

}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\relation;

use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IRelationField;
use umi\orm\metadata\field\TRelationField;
use umi\orm\object\IObject;
use umi\orm\persister\IObjectPersisterAware;
use umi\orm\persister\TObjectPersisterAware;

/**
 * Класс поля хранителя связи.
 */
class BelongsToRelationField extends BaseField
    implements IRelationField, ICollectionManagerAware, IObjectManagerAware, IObjectPersisterAware
{

    use TRelationField;
    use TCollectionManagerAware;
    use TObjectManagerAware;
    use TObjectPersisterAware;

    /**
     * {@inheritdoc}
     */
    public function getTargetCollectionName()
    {
        return $this->targetCollectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetCollection()
    {
        return $this->getCollectionManager()
            ->getCollection($this->targetCollectionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'integer';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        if (!$propertyValue instanceof IObject) {
            throw new InvalidArgumentException($this->translate(
                'Value must be instance of IObject.'
            ));
        }

        if (!$this->getTargetCollection()->contains($propertyValue)) {
            throw new InvalidArgumentException($this->translate(
                'Object from wrong collection is given.'
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        if (is_null($internalDbValue)) {
            return null;
        }

        return $this->getTargetCollection()->getById($internalDbValue);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        if (!$propertyValue instanceof IObject) {
            return null;
        }

        if (!$propertyValue->getId()) {
            $this->getObjectPersister()
                ->storeNewBelongsToRelation($this, $object, $propertyValue);
        }

        return $propertyValue->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyConfiguration(array $config)
    {
        $this->applyTargetCollectionConfig($config);
    }
}

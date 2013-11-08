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
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IRelationField;
use umi\orm\metadata\field\TRelationField;
use umi\orm\object\IObject;

/**
 * Класс поля связи "один-ко-многим".
 */
class HasManyRelationField extends BaseField implements IRelationField, ICollectionManagerAware
{

    use TRelationField;
    use TCollectionManagerAware;

    /**
     * {@inheritdoc}
     */
    public function getTargetCollectionName()
    {
        return $this->targetCollectionName;
    }

    /**
     * Возвращает имя поля для связи с target-коллекцией
     * @return string
     */
    public function getTargetFieldName()
    {
        return $this->targetFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyConfiguration(array $config)
    {
        $this->applyTargetCollectionConfig($config);
        $this->applyTargetFieldConfig($config);
    }

    /**
     *{@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        throw new NotAllowedOperationException($this->translate(
            'Cannot set value for property "{name}". Value should be set on relation owner side.',
            ['name' => $this->getName()]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        $collectionName = $this->getTargetCollectionName();
        $targetCollection = $this->getCollectionManager()
            ->getCollection($collectionName);
        $targetFieldName = $this->getTargetFieldName();

        return $targetCollection->select()
            ->where($targetFieldName)
            ->equals($object->getId())
            ->result();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        return null;
    }

}

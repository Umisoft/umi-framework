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
use umi\orm\object\property\ICounterProperty;

/**
 * Простая коллекция иерархических объектов.
 */
class SimpleHierarchicCollection extends BaseHierarchicCollection implements ISimpleHierarchicCollection
{
    /**
     * {@inheritdoc}
     */
    public function add($slug, $typeName = IObjectType::BASE, IHierarchicObject $branch = null)
    {
        if (!$this->metadata->getTypeExists($typeName)) {
            throw new NonexistentEntityException($this->translate(
                'Cannot add object in collection "{collection}". Object type "{type}" does not exist.',
                ['collection' => $this->name, 'type' => $typeName]
            ));
        }

        /**
         * @var IHierarchicObject $object
         */
        $object = $this->getObjectManager()
            ->registerNewObject($this, $this->metadata->getType($typeName));
        $object->getProperty(IHierarchicObject::FIELD_SLUG)
            ->setValue($slug);
        if ($branch) {
            $object->getProperty(IHierarchicObject::FIELD_PARENT)
                ->setValue($branch);
            /**
             * @var ICounterProperty $childCountProperty
             */
            $childCountProperty = $branch->getProperty(IHierarchicObject::FIELD_CHILD_COUNT);
            $childCountProperty->increment();
        }

        return $object;
    }
}

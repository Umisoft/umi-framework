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

/**
 * Коллекция простых объектов.
 */
class SimpleCollection extends BaseCollection implements ISimpleCollection
{

    /**
     * {@inheritdoc}
     */
    public function add($typeName = IObjectType::BASE)
    {
        if (!$this->metadata->getTypeExists($typeName)) {
            throw new NonexistentEntityException($this->translate(
                'Cannot add object in collection "{collection}". Object type "{type}" does not exist.',
                ['collection' => $this->name, 'type' => $typeName]
            ));
        }

        return $this->getObjectManager()
            ->registerNewObject($this, $this->metadata->getType($typeName));
    }
}

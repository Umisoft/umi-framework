<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\relation;

use umi\orm\metadata\field\IRelationField;
use umi\orm\object\IObject;

/**
 * Класс поля связи "один-к-одному".
 */
class HasOneRelationField extends HasManyRelationField implements IRelationField
{

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        $targetFieldName = $this->getTargetFieldName();

        return $this->getTargetCollection()->select()
            ->where($targetFieldName)
            ->equals($object->getId())
            ->limit(1)
            ->result()
            ->fetch();
    }
}

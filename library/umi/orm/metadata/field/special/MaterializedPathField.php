<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\special;

use umi\orm\exception\RuntimeException;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\ICalculableField;
use umi\orm\metadata\field\IScalarField;
use umi\orm\metadata\field\TCalculableField;
use umi\orm\metadata\field\TScalarField;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;

/**
 * Материализованный путь объекта (mpath).
 */
class MaterializedPathField extends BaseField implements IScalarField, ICalculableField
{

    /**
     * Разделитель для materialized path
     */
    const MPATH_SEPARATOR = '.';
    /**
     * Начальный символ для materialized path
     */
    const MPATH_START_SYMBOL = '#';

    use TScalarField;
    use TCalculableField;

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        return true; // TODO: not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDBValue(IObject $object)
    {

        if (!$object instanceof IHierarchicObject) {
            throw new RuntimeException($this->translate(
                'Cannot calculate materialized path value for nonhierarchical object.'
            ));
        }

        if (null != ($mpath = $object->getProperty($this->getName())
                ->getDbValue())
        ) {
            return $mpath;
        }

        if (($parent = $object->getParent()) && $parent->getMaterializedPath()) {
            return $parent->getMaterializedPath() . self::MPATH_SEPARATOR . $object->getId();
        } else {
            return self::MPATH_START_SYMBOL . $object->getId();
        }
    }
}

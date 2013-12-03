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
 * Класс поля для хранения URI.
 */
class UriField extends BaseField implements IScalarField, ICalculableField
{

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
        return is_string($propertyValue); // TODO: check uri format
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDBValue(IObject $object)
    {

        if (!$object instanceof IHierarchicObject) {
            throw new RuntimeException($this->translate(
                'Cannot calculate URI value for nonhierarchical object.'
            ));
        }
        if (null != ($uri = $object->getProperty($this->getName())
                ->getDbValue())
        ) {
            return $uri;
        }

        if (($parent = $object->getParent()) && $parent->getURI()) {
            return $parent->getURI() . '/' . $object->getSlug();
        } else {
            return '//' . $object->getSlug();
        }
    }
}

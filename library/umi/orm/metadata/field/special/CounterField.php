<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\special;

use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\ICalculableField;
use umi\orm\metadata\field\IScalarField;
use umi\orm\metadata\field\TScalarField;
use umi\orm\object\IObject;
use umi\orm\object\property\IProperty;

/**
 * Класс поля со счетчиком.
 */
class CounterField extends BaseField implements IScalarField, ICalculableField
{

    use TScalarField;

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
        return is_int($propertyValue); // TODO: not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDBValue(IObject $object)
    {
        return $object->getProperty($this->getName())
            ->getDbValue();
    }

    /**
     * {@inheritdoc}
     */
    public function persistProperty(IObject $object, IProperty $property, IQueryBuilder $builder)
    {

        /**
         * @var IUpdateBuilder $builder
         */
        if ($builder instanceof IUpdateBuilder) {
            $increment = $property->getDbValue() - $property->getPreviousDbValue();
            if ($increment !== 0) {

                $incrementExpression = $builder->getConnection()
                        ->quoteIdentifier($this->getColumnName()) . ' + (' . $increment . ')';
                $builder
                    ->set($this->getColumnName(), ':new' . $this->getColumnName())
                    ->bindExpression(':new' . $this->getColumnName(), $incrementExpression);
            }
        } elseif ($builder instanceof IInsertBuilder) {
            $builder->set($this->getColumnName());
            $builder->bindValue(':' . $this->getColumnName(), $this->calculateDBValue($object), $this->getDataType());
        }

        return $this;
    }

}

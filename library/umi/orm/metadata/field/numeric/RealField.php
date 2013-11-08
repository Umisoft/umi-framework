<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\numeric;

use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IScalarField;
use umi\orm\metadata\field\TScalarField;

/**
 * Класс поля для числовых данных с плавающей запятой.
 */
class RealField extends BaseField implements IScalarField
{

    use TScalarField;

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'float';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        return is_float($propertyValue) || is_int($propertyValue);
    }
}

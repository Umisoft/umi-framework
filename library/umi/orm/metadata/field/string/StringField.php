<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\string;

use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\metadata\field\IScalarField;
use umi\orm\metadata\field\TLocalizableField;
use umi\orm\metadata\field\TScalarField;

/**
 * Класс поля для строковых данных переменной длины.
 */
class StringField extends BaseField implements IScalarField, ILocalizableField, ILocalesAware
{

    use TScalarField;
    use TLocalizableField;
    use TLocalesAware;

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
        return is_string($propertyValue);
    }

}

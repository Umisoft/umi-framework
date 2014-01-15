<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

/**
 * Фабрика свойств объекта.
 */
interface IPropertyFactory
{
    /**
     * Создает экземпляр свойства для указанного объекта
     * @param IObject $object объект
     * @param IField $field поле типа данных
     * @param null|string $localeId идентификатор локали для свойства
     * @return IProperty
     */
    public function createProperty(IObject $object, IField $field, $localeId = null);
}

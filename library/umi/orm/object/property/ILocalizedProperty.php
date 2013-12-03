<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

/**
 * Интерфейс свойства, имеющего локализацию.
 */
interface ILocalizedProperty extends IProperty
{

    /**
     * Разделитель для локали поля
     */
    const LOCALE_SEPARATOR = '#';

    /**
     * Возвращает идентификатор локали для локализованного свойства
     * @return null|string null, если свойство не локализовано либо не имеет локалей
     */
    public function getLocaleId();
}

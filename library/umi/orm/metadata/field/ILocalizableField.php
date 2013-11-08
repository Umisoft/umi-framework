<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\orm\exception\NonexistentEntityException;

/**
 * Интерфейс поля, имеющего локализации.
 */
interface ILocalizableField extends IField
{

    /**
     * Проверяет, локазизовано ли поле
     * @return bool
     */
    public function getIsLocalized();

    /**
     * Возвращает список локализаций для поля
     * @return array в виде array($localeId => array('column' => $columnName, 'default' => $defaultValue), ...)
     */
    public function getLocalizations();

    /**
     * Проверяет, есть ли указанная локаль у поля
     * @param string $localeId идентификатор локали
     * @return bool
     */
    public function hasLocale($localeId);

    /**
     * Возвращает имя столбца таблицы для поля с учетом локали
     * @param string|null $localeId идентификатор локали
     * @throws NonexistentEntityException если не найдено имя столбца для указанной локали
     * @return string
     */
    public function getLocaleColumnName($localeId = null);

    /**
     * Возвращает значение поля по умолчанию (которое будет сохраняться в БД при создании объекта) с учетом локали
     * @param string|null $localeId идентификатор локали
     * @throws NonexistentEntityException если не найдено значения для указанной локали
     * @return string
     */
    public function getLocaleDefaultValue($localeId = null);

}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n;

/**
 * Сервис для работы с локалями
 */
interface ILocalesService
{

    /**
     * Возвращает локаль по умолчанию
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Возвращает текущую локаль
     * @return string
     */
    public function getCurrentLocale();

    /**
     * Устанавливает локаль по умолчанию
     * @param string $localeId
     * @return $this
     */
    public function setDefaultLocale($localeId);

    /**
     * Устанавливает текущую локаль
     * @param string $localeId
     * @return $this
     */
    public function setCurrentLocale($localeId);

}
 
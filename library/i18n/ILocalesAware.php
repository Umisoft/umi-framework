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
 * Интерфейс для поддержки локалей.
 */
interface ILocalesAware
{

    /**
     * Внедряет сервис для работы с локалями
     * @param ILocalesService $localesService
     * @return self
     */
    public function setLocalesService(ILocalesService $localesService);
}

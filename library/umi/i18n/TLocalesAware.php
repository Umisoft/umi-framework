<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n;

use umi\i18n\exception\RequiredDependencyException;

/**
 * Трейт для поддержки локалей.
 */
trait TLocalesAware
{
    /**
     * @var ILocalesService $_localesService сервис для работы с локалями
     */
    private $_localesService;

    /**
     * Внедряет сервис для работы с локалями
     * @param ILocalesService $localesService
     * @return self
     */
    public function setLocalesService(ILocalesService $localesService)
    {
        $this->_localesService = $localesService;

        return $this;
    }

    /**
     * Возвращает локаль по умолчанию
     * @return string
     */
    protected function getDefaultLocale()
    {
        return $this->getLocalesService()
            ->getDefaultLocale();
    }

    /**
     * Возвращает текущую локаль
     * @return string
     */
    protected function getCurrentLocale()
    {
        return $this->getLocalesService()
            ->getCurrentLocale();
    }

    /**
     * Возвращает сервис для работы с локалями
     * @throws RequiredDependencyException
     * @return ILocalesService
     */
    private function getLocalesService()
    {
        if (!$this->_localesService) {
            throw new RequiredDependencyException(sprintf(
                'Locales service is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_localesService;
    }
}

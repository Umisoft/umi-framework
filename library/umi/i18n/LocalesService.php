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
class LocalesService implements ILocalesService
{

    /**
     * @var string $defaultLocale локаль по умолчанию
     */
    protected $defaultLocale = 'en-US';
    /**
     * @var string $currentLocale текущая локаль
     */
    protected $currentLocale = 'en-US';

    /**
     * {@inheritdoc}
     */
    public function setDefaultLocale($localeId)
    {
        $this->defaultLocale = $localeId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($localeId)
    {
        $this->currentLocale = $localeId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        if (!$this->currentLocale) {
            return $this->defaultLocale;
        }

        return $this->currentLocale;
    }
}
 
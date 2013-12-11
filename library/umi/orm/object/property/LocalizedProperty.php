<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

/**
 * Класс свойства, имеющего локализацию
 */
class LocalizedProperty extends BaseProperty implements ILocalizedProperty, ILocalesAware
{

    use TLocalesAware;

    /**
     * @var string $localeId идентификатор локали
     */
    protected $localeId;

    /**
     * Конструктор
     * @param IObject $object владелец свойства
     * @param IField $field поле типа данных
     * @param string $localeId идентификатор локали
     */
    public function __construct(IObject $object, IField $field, $localeId)
    {
        $this->object = $object;
        $this->field = $field;
        $this->localeId = $localeId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleId()
    {
        return $this->localeId;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        return $this->field->getName() . self::LOCALE_SEPARATOR . $this->localeId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDbValue()
    {
        if (!$this->getIsLoaded()) {
            $loadLocalization = $this->localeId !== $this->getCurrentLocale()
                && $this->localeId !== $this->getDefaultLocale();
            $this->object->fullyLoad($loadLocalization);
        }

        return $this->dbValue;
    }
}

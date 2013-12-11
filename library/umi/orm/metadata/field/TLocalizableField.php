<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\UnexpectedValueException;
use umi\orm\object\IObject;
use umi\orm\object\property\ILocalizedProperty;
use umi\orm\object\property\IProperty;

/**
 * Трейт для локализуемого поля.
 */
trait TLocalizableField
{

    /**
     * @var array $localizations список локализаций в виде
     * [$localeId => ['columnName' => $columnName, 'defaultValue' => $defaultValue], ...]
     */
    protected $localizations = [];

    /**
     * Возвращает имя поля
     * @return string
     */
    abstract public function getName();

    /**
     * Возвращает php-тип данных поля. Используется для PDO.<br />
     * http://ru2.php.net/manual/en/function.gettype.php
     * @return string
     */
    abstract public function getDataType();

    /**
     * Возвращает имя столбца таблицы для поля
     * @return string
     */
    abstract public function getColumnName();

    /**
     * Возвращает значение поля по умолчанию (которое будет сохраняться в БД при создании объекта)
     * @return string
     */
    abstract public function getDefaultValue();

    /**
     * Возвращает сообщение из указанного словаря, переведенное для текущей или указанной локали.
     * Текст сообщения может содержать плейсхолдеры. Ex: File "{path}" not found
     * Если идентификатор локали не указан, будет использована текущая локаль.
     * @param string $message текст сообщения на языке разработки
     * @param array $placeholders значения плейсхолдеров для сообщения. Ex: array('{path}' => '/path/to/file')
     * @param string $localeId идентификатор локали в которую осуществляется перевод (ru, en_us)
     * @return string
     */
    abstract protected function translate($message, array $placeholders = [], $localeId = null);

    /**
     * Возвращает текущую локаль
     * @return string
     */
    abstract protected function getCurrentLocale();

    /**
     * Проверяет, локазизовано ли поле
     * @return bool
     */
    public function getIsLocalized()
    {
        return (bool) count($this->getLocalizations());
    }

    /**
     * Возвращает список локализаций для поля
     * @return array в виде array($localeId => array('column' => $columnName, 'default' => $defaultValue), ...)
     */
    public function getLocalizations()
    {
        return $this->localizations;
    }

    /**
     * Проверяет, есть ли указанная локаль у поля
     * @param string $localeId идентификатор локали
     * @return bool
     */
    public function hasLocale($localeId)
    {
        return isset($this->localizations[$localeId]);
    }

    /**
     * Возвращает имя столбца таблицы для поля с учетом локали
     * @param string|null $localeId идентификатор локали
     * @throws NonexistentEntityException если не найдено имя столбца для указанной локали
     * @return string
     */
    public function getLocaleColumnName($localeId = null)
    {
        if (!$localeId) {
            return $this->getColumnName();
        }

        if (!isset($this->localizations[$localeId]) || !isset($this->localizations[$localeId]['columnName'])) {
            throw new NonexistentEntityException($this->translate(
                'Information about column name for field "{field}" in locale "{locale}" does not exist.',
                ['field' => $this->getName(), 'locale' => $localeId]
            ));
        }

        return $this->localizations[$localeId]['columnName'];
    }

    /**
     * Возвращает значение поля по умолчанию (которое будет сохраняться в БД при создании объекта) с учетом локали
     * @param string|null $localeId идентификатор локали
     * @throws NonexistentEntityException если не найдено значения для указанной локали
     * @return string
     */
    public function getLocaleDefaultValue($localeId = null)
    {
        if (!$localeId) {
            return $this->getDefaultValue();
        }

        if (!isset($this->localizations[$localeId])) {
            throw new NonexistentEntityException($this->translate(
                'Cannot get default value for field "{field}" in locale "{locale}".',
                ['field' => $this->getName(), 'locale' => $localeId]
            ));
        }

        $defaultValue =
            isset($this->localizations[$localeId]['defaultValue'])
                ? $this->localizations[$localeId]['defaultValue']
                : null;

        return $defaultValue;
    }

    /**
     * Дополняет запрос условием на изменение значения свойства в БД.
     * @internal
     * @param IObject $object объект, для которого выставляется значение
     * @param IProperty $property свойство, для которого выставляется значение
     * @param IQueryBuilder $builder построитель запросов, с помощью которого изменяется значние
     * @return $this
     */
    public function persistProperty(IObject $object, IProperty $property, IQueryBuilder $builder)
    {
        /**
         * @var IUpdateBuilder $builder
         */
        if ($builder instanceof IInsertBuilder || $builder instanceof IUpdateBuilder) {

            $localeId = $property instanceof ILocalizedProperty ? $property->getLocaleId() : null;

            if ($localeId && !$this->hasLocale($localeId)) {
                return $this;
            }

            $builder->set($this->getLocaleColumnName($localeId));
            $builder->bindValue(
                ':' . $this->getLocaleColumnName($localeId),
                $property->getDbValue(),
                $this->getDataType()
            );

        }

        return $this;
    }

    /**
     * Разбирает и применяет конфигурацию для поля
     * @param array $config конфигурация поля
     */
    protected function applyConfiguration(array $config)
    {
        $this->applyLocalizationsConfig($config);
    }

    /**
     * Разбирает и применяет конфигурацию для локализации поля
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException при ошибках в конфигурации
     */
    protected function applyLocalizationsConfig($config)
    {
        if (isset($config['localizations'])) {
            $localizations = $config['localizations'];
            if (!is_array($localizations)) {
                throw new UnexpectedValueException($this->translate(
                    'Localization configuration for localizable field should be an array.'
                ));
            }
            $this->localizations = $localizations;
        }
    }
}

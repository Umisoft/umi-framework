<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation;

use umi\validation\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки работы с валидаторами.
 */
trait TValidationAware
{
    /**
     * @var IValidatorFactory $_validatorFactory фабрика для создания валидаторов.
     */
    private $_validatorFactory;

    /**
     * Устанавливает фабрику валидаторов.
     * @param IValidatorFactory $validatorFactory
     */
    public final function setValidatorFactory(IValidatorFactory $validatorFactory)
    {
        $this->_validatorFactory = $validatorFactory;
    }

    /**
     * Создает коллекцию валидаторов на основе массива.
     * @example ['regexp' => ['pattern' => '/[0-9]+/']]
     * @param array $config конфигурация валидаторов
     * @throws RequiredDependencyException если инструменты для валидации не установлены
     * @return IValidatorCollection
     */
    protected final function createValidatorCollection(array $config = [])
    {
        return $this->getValidatorFactory()
            ->createValidatorCollection($config);
    }

    /**
     * Создает валидатор определенного типа. Устанавливет ему опции (если переданы).
     * @param string $type тип валидатора
     * @param array $options опции валидатора
     * @throws RequiredDependencyException если инструменты для валидации не установлены
     * @return IValidator созданный валидатор
     */
    protected final function createValidator($type, array $options = [])
    {
        return $this->getValidatorFactory()
            ->createValidator($type, $options);
    }

    /**
     * Возвращает фабрику валидаторов.
     * @return IValidatorFactory
     * @throws RequiredDependencyException
     */
    private final function getValidatorFactory()
    {
        if (!$this->_validatorFactory) {
            throw new RequiredDependencyException(sprintf(
                'Validator factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_validatorFactory;
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation;

use umi\validation\exception\OutOfBoundsException;

/**
 * Интерфейс фабрики создания валидаторов.
 */
interface IValidatorFactory
{
    /**
     * Валидатор email адреса. Не принимает опции
     */
    const TYPE_EMAIL = "email";
    /**
     * Валидатор обязательного значения. Не принимает опций.
     */
    const TYPE_REQUIRED = "required";
    /**
     * Валидатор по регулярному выражению.
     * Опции:
     *    pattern => [регулярное выражение]
     */
    const TYPE_REGEXP = "regexp";

    /**
     * Создает коллекцию валидаторов на основе массива
     * @example ['regexp' => ['pattern' => '/[0-9]+/']]
     * @param array $config конфигурация валидаторов
     * @return IValidatorCollection
     */
    public function createValidatorCollection(array $config);

    /**
     * Создает валидатор определенного типа. Устанавливет ему опции (если переданы).
     * @param string $type тип валидатора
     * @param array $options опции валидатора
     * @throws OutOfBoundsException если тип валидатора не найден
     * @return IValidator
     */
    public function createValidator($type, array $options = []);
}
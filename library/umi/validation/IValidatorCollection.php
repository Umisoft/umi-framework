<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation;

/**
 * Интерфейс валидации.
 */
interface IValidatorCollection extends IValidator
{
    /**
     * Добавляет валидатор в конец цепочки валидаторов.
     * @param IValidator $validator валидатор
     * @return self
     */
    public function appendValidator(IValidator $validator);

    /**
     * Добавляет валидатор в начало цепочки валидаторов.
     * @param IValidator $validator валидатор
     * @return self
     */
    public function prependValidator(IValidator $validator);
}
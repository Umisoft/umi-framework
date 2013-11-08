<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element;

use umi\filter\IFilterCollection;
use umi\form\IFormEntity;
use umi\validation\IValidatorCollection;

/**
 * Интерфейс элемента формы.
 */
interface IElement extends IFormEntity
{
    /**
     * Возвращает значение элемента.
     * @return string
     */
    public function getValue();

    /**
     * Устанавливает значение элемента.
     * @param mixed $value значение
     * @param bool $isRaw если true фильтры применяться не будут
     * @return self
     */
    public function setValue($value, $isRaw = false);

    /**
     * Устанавливает цепочку фильтров.
     * @param IFilterCollection $filters фильтры
     * @return self
     */
    public function setFilters(IFilterCollection $filters);

    /**
     * Возвращает установленные валидаторы для формы.
     * @return IFilterCollection
     */
    public function getFilters();

    /**
     * Устанавливает цепочку валидаторов для элемента формы.
     * @param IValidatorCollection $validators
     * @return self
     */
    public function setValidators(IValidatorCollection $validators);

    /**
     * Возвращает установленные валидаторы для формы.
     * @return IValidatorCollection
     */
    public function getValidators();
}
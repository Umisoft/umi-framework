<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\mock;

use umi\validation\IValidator;
use umi\validation\IValidatorCollection;

/**
 * Мок-класс коллкции валидаторов
 */
class ValidatorCollectionFixture implements IValidatorCollection
{

    protected $collection;

    /**
     * Конструктор
     * @param array $validators валидаторы
     */
    public function __construct(array $validators)
    {
        $this->collection = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($var)
    {
        return $this;
    }

    /**
     * Добавляет валидатор в конец цепочки валидаторов.
     * @param IValidator $validator валидатор
     * @return self
     */
    public function appendValidator(IValidator $validator)
    {
        array_push($this->collection, $validator);

        return $this;
    }

    /**
     * Добавляет валидатор в начало цепочки валидаторов.
     * @param IValidator $validator валидатор
     * @return self
     */
    public function prependValidator(IValidator $validator)
    {
        array_unshift($this->collection, $validator);

        return $this;
    }
}
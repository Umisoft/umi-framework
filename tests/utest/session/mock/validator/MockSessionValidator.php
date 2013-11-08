<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\mock\validator;

use umi\session\entity\ns\ISessionNamespace;
use umi\session\entity\validator\ISessionValidator;

/**
 * Мок валидатор сессии.
 */
class MockSessionValidator implements ISessionValidator
{
    /**
     * @var bool $result результат работы валидатора
     */
    protected $result;

    /**
     * Конструктор.
     * @param bool $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ISessionNamespace $namespace)
    {
        return $this->result;
    }
}
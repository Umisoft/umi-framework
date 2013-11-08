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

/**
 * Мок-класс валидатора
 */
class ValidatorFixture2 implements IValidator
{

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
        return true;
    }
}
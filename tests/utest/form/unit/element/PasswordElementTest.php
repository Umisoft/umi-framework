<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\Password;

/**
 * Тесты элемента формы - Пароль
 */
class PasswordElementTest extends BaseElementTest
{
    /**
     * {@inheritdoc}
     */
    public function getElement($name, array $attributes = [], array $options = [])
    {
        $password = new Password($name, $attributes, $options);

        $this->resolveOptionalDependencies($password);

        return $password;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\unit\type;

use umi\validation\IValidator;
use umi\validation\type\Required;
use utest\validation\ValidationTestCase;

/**
 * Класс RequiredValidatorTests
 */
class RequiredValidatorTests extends ValidationTestCase
{

    /**
     * @var IValidator $validator
     */
    private $validator = null;

    public function setUpFixtures()
    {
        $this->validator = new Required();
    }

    public function testValidate()
    {
        $this->assertTrue(
            $this->validator->isValid("not empty string"),
            "Ожидается, что непустая строка пройдет валидацию"
        );
        $this->assertFalse($this->validator->isValid(""), "Ожидается, что пустая строка не пройдет валидацию");
    }

    public function testMessages()
    {
        $this->validator->isValid("not empty string");
        $this->assertEmpty($this->validator->getMessages(), "Ожидается, что сообщений об ошибках не будет");

        $this->validator->isValid("");
        $this->assertContains(
            'Value is required.',
            $this->validator->getMessages(),
            "Ожидается, что будет сообщение о неверной валидации"
        );
    }
}
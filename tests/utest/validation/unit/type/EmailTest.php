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
use umi\validation\type\Email;
use utest\validation\ValidationTestCase;

/**
 * Класс EmailValidatorTests
 */
class EmailValidatorTests extends ValidationTestCase
{

    /**
     * @var IValidator $validator
     */
    private $validator = null;

    public function setUpFixtures()
    {
        $this->validator = new Email();
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->isValid("email@example.com"), "Ожидается, что email пройдет валидацию");
        $this->assertFalse($this->validator->isValid("not a email"), "Ожидается, что строка не пройдет валидацию");
    }

    public function testMessages()
    {
        $this->validator->isValid("email@example.com");
        $this->assertEmpty($this->validator->getMessages(), "Ожидается, что сообщений об ошибках не будет");

        $this->validator->isValid("not a email");
        $this->assertContains(
            'Wrong email format.',
            $this->validator->getMessages(),
            "Ожидается, что будет сообщение о неверной валидации email"
        );
    }
}
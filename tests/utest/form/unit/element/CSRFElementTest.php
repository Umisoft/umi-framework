<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\form\element\CSRF;
use utest\form\FormTestCase;

/**
 * Тесты CSRF элемента формы.
 */
class CSRFElementTest extends FormTestCase
{
    /**
     * @var CSRF $csrf
     */
    protected $csrf;

    public function setUpFixtures()
    {
        $this->csrf = new CSRF('test');
        $this->resolveOptionalDependencies($this->csrf);
    }

    public function testBasic()
    {
        $this->assertFalse($this->csrf->isValid(), 'Ожидается, что CSRF с неустановленным токеном неверный.');

        $val = $this->csrf->getValue();
        $this->csrf->setValue($val);
        $this->assertTrue($this->csrf->isValid(), 'Ожидается, что установленный токен верный.');

        $this->csrf->setValue($val . 'abacaba');
        $this->assertFalse($this->csrf->isValid(), 'Ожидается, что значение токена не верно.');
    }
}
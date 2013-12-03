<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\form;

use utest\event\TEventSupport;
use utest\filter\TFilterSupport;
use utest\http\THttpSupport;
use utest\session\TSessionSupport;
use utest\TestCase;
use utest\validation\TValidationSupport;

/**
 * Тест кейс для форм
 */
abstract class FormTestCase extends TestCase
{
    use TFormSupport;
    use TEventSupport;
    use TFilterSupport;
    use THttpSupport;
    use TSessionSupport;
    use TValidationSupport;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerEventTools();
        $this->registerFilterTools();
        $this->registerHttpTools();
        $this->registerSessionTools();
        $this->registerValidationTools();
        $this->registerFormTools();

        parent::setUp();
    }
}
 
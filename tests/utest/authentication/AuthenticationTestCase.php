<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\authentication;

use utest\dbal\TDbalSupport;
use utest\event\TEventSupport;
use utest\http\THttpSupport;
use utest\session\TSessionSupport;
use utest\TestCase;

/**
 * Тест кейс для аутентификации
 */
abstract class AuthenticationTestCase extends TestCase
{
    use TAuthenticationSupport;
    use TEventSupport;
    use THttpSupport;
    use TDbalSupport;
    use TSessionSupport;
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerEventTools();
        $this->registerHttpTools();
        $this->registerSessionTools();
        $this->registerDbalTools();
        $this->registerAuthenticationTools();

        parent::setUp();
    }
}
 
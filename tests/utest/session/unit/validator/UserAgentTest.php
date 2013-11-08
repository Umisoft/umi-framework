<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\validator;

use umi\http\request\Request;
use umi\session\entity\ns\SessionNamespace;
use umi\session\entity\validator\UserAgent;
use utest\session\SessionTestCase;

/**
 * Класс валидатора Lifetime
 */
class UserAgentTest extends SessionTestCase
{

    /**
     * @var UserAgent $validator
     */
    protected $validator;

    public function setUpFixtures()
    {
        $request = new Request();
        $this->resolveOptionalDependencies($request);

        $this->validator = new UserAgent();
    }

    public function testBasic()
    {
        $session = new SessionNamespace('test');
        $this->assertTrue($this->validator->validate($session), 'Ожидается, что валидация будет успешной');

        $session->setMetadata(UserAgent::META_KEY, 'Invalid agent');
        $this->assertFalse($this->validator->validate($session));
    }
}
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
use umi\session\entity\validator\Lifetime;
use umi\session\exception\RuntimeException;
use utest\session\SessionTestCase;

/**
 * Класс валидатора Lifetime
 */
class LifetimeTest extends SessionTestCase
{
    /**
     * @var Lifetime $validator
     */
    protected $validator;

    public function setUpFixtures()
    {
        $request = new Request();
        $this->resolveOptionalDependencies($request);

        $this->validator = new Lifetime(60);
        $this->resolveOptionalDependencies($this->validator);
    }

    public function testBasic()
    {
        $namespace = new SessionNamespace('default');

        $this->assertTrue(
            $this->validator->validate($namespace),
            'Ожидается, что валидация новой сессии будет успешна.'
        );

        $namespace->setMetadata(Lifetime::META_KEY, $namespace->getMetadata(Lifetime::META_KEY) - 120);

        $this->assertFalse($this->validator->validate($namespace));
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongLifetime()
    {
        new Lifetime(-12);
    }
}
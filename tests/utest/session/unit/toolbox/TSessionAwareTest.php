<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\toolbox;

use umi\session\ISessionAware;
use umi\session\TSessionAware;
use utest\session\SessionTestCase;

/**
 * Тесты трейта TSessionAware
 */
class TSessionAwareTest extends SessionTestCase implements ISessionAware
{

    use TSessionAware;

    public function setUpFixtures()
    {
        $this->resolveOptionalDependencies($this);
    }

    public function testSessionNamespace()
    {
        $this->assertFalse($this->hasSessionNamespace('test'), 'Ожидается, что незарегистрированное пространство имен не существует');

        $e = null;
        try {
            $this->getSessionNamespace('test');
        } catch (\Exception $e) { }
        $this->assertInstanceOf(
            'umi\session\exception\OutOfBoundsException',
            $e,
            'Ожидается исключение при попытке получить незарегистрированное пространство имен'
        );

        $this->assertInstanceOf('utest\session\unit\toolbox\TSessionAwareTest', $this->registerSessionNamespace('test'));

        $this->assertTrue($this->hasSessionNamespace('test'), 'Ожидается, что зарегистрированное пространство имен существует');

        $this->assertInstanceOf(
            'umi\session\entity\ns\ISessionNamespace',
            $this->getSessionNamespace('test'),
            'Ожидается, что можно получить зарегистрированное пространство имен'
        );

        $this->assertInstanceOf(
            'umi\session\entity\ns\ISessionNamespace',
            $this->getSessionNamespace('newTest', true),
            'Ожидается, что можно зарегистрировать пространство имен при получении'
        );
    }

}
 
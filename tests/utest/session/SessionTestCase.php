<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session;

use utest\TestCase;

/**
 * Test case для тестирования сессии.
 */
abstract class SessionTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->getTestToolkit()->registerToolboxes([
            require(LIBRARY_PATH . '/http/toolbox/config.php'),
            require(LIBRARY_PATH . '/session/toolbox/config.php')
        ]);

        @session_destroy();

        ini_set('session.use_cookies', 0);
        ini_set('session.cache_limiter', '');

        parent::setUp();
    }

    public function tearDown()
    {
        @session_destroy();

        ini_restore('session.use_cookies');
        ini_restore('session.cache_limiter');

        parent::setUp();
    }
}
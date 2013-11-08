<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\toolbox;

use umi\session\toolbox\ISessionTools;
use utest\TestCase;

/**
 * Тест инструментов для работы с сессиями
 */
class SessionToolsTest extends TestCase
{
    /**
     * @var ISessionTools $sessionTools
     */
    protected $sessionTools;

    public function setUpFixtures()
    {
        $this->sessionTools = $this->getTestToolkit()
            ->getToolbox(ISessionTools::ALIAS);
    }

    public function testGetManager()
    {
        $this->assertInstanceOf(
            'umi\session\ISessionManager',
            $this->sessionTools->getManager(),
            "Ожидается, что вернется объект ISessionManager"
        );
    }

    public function testGetNSFactory()
    {
        $this->assertInstanceOf(
            'umi\session\ISession',
            $this->sessionTools->getSession(),
            "Ожидается, что вернется объект ISession"
        );
    }

}
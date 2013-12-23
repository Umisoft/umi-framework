<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\unit;

use umi\spl\config\TConfigSupport;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\PrototypeFactory;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;
use umi\toolkit\Toolkit;
use utest\TestCase;

/**
 * Тестирование набора инструментов
 */
class ToolboxTest extends TestCase implements IToolbox
{

    use TToolbox;

    /**
     * Метод для создания специфического окружения тест-кейса.
     * Может быть перегружен в конкретном тест-кейсе, если это необходимо
     */
    protected function setUpFixtures()
    {
        $toolkit = new Toolkit();
        $prototypeFactory = new PrototypeFactory($toolkit);

        $toolkit->setPrototypeFactory($prototypeFactory);
        $this->setToolkit($toolkit);
        $this->setPrototypeFactory($prototypeFactory);
    }

    public function testMethods()
    {
        $e = null;
        try {
            $this->getService(null, null);
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf('umi\toolkit\exception\UnsupportedServiceException', $e);
    }

}



<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\component;

use umi\hmvc\component\response\ComponentResponse;
use umi\hmvc\component\response\IComponentResponse;
use utest\TestCase;

/**
 * Class ComponentResponseTest
 */
class ComponentResponseTest extends TestCase
{
    /**
     * @var IComponentResponse $response
     */
    private $response;

    public function setUpFixtures()
    {
        $this->response = new ComponentResponse();
        $this->resolveOptionalDependencies($this->response);
    }

    public function testProcessing()
    {
        $this->assertTrue(
            $this->response->isProcessable(),
            'Ожидается, что по умолчанию результат работы компонента True.'
        );

        $this->assertSame($this->response, $this->response->stopProcessing());

        $this->assertFalse($this->response->isProcessable(), 'Ожидается, что результат работы компонента False.');
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\controller;

use umi\hmvc\component\response\IComponentResponse;
use umi\hmvc\controller\IController;
use umi\hmvc\component\response\model\IDisplayModel;
use utest\hmvc\HMVCTestCase;
use utest\hmvc\mock\controller\MockStaticPageController;

/**
 * Class StaticPageControllerTest
 */
class StaticPageControllerTest extends HMVCTestCase
{
    /**
     * @var IController $controller
     */
    private $controller;

    public function setUpFixtures()
    {
        $this->controller = new MockStaticPageController();
        $this->resolveOptionalDependencies($this->controller);
    }

    public function testController()
    {
        $controller = $this->controller;
        /**
         * @var IComponentResponse $response
         */
        $response = $controller($this->getRequest('/'));

        $this->assertEquals(200, $response->getCode());

        /**
         * @var IDisplayModel $content
         */
        $content = $response->getContent();
        $this->assertEquals('mock', $content->getTemplate());
        $this->assertEmpty($content->getVariables());
    }

}

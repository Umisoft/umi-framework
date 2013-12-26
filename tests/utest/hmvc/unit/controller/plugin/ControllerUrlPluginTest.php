<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\hmvc\unit\controller\plugin;

use umi\hmvc\component\IComponent;
use umi\hmvc\context\Context;
use umi\http\request\IRequest;
use utest\hmvc\HMVCTestCase;
use utest\hmvc\mock\controller\plugin\MockURLController;

/**
 * Тесты URL плагина контроллера.
 */
class ControllerUrlPluginTestTest extends HMVCTestCase
{
    /**
     * @var MockURLController $urlController
     */
    protected $urlController;

    public function setUpFixtures()
    {
        $this->urlController = new MockURLController();

        $component = $this->getComponent(
            [
                IComponent::OPTION_ROUTES => [
                    'home'       => [
                        'type' => 'fixed'
                    ],
                    'additional' => [
                        'type'  => 'simple',
                        'route' => '/additional/{param}'
                    ]
                ]
            ]
        );

        $request = $this->getRequest('/additional');
        $request->getParams(IRequest::HEADERS)->set('HTTP_HOST', 'example.com');
        $request->getParams(IRequest::HEADERS)->set('HTTPS', 'On');
        $request->setRouteParams(['param' => 'hello-world']);

        $this->urlController->setContext(new Context($component, $request));
    }

    public function tearDownFixtures()
    {
        unset($_SERVER['HTTPS']);
    }

    public function testURL()
    {
        $this->assertEquals('/', $this->urlController->pluginUrl('home'));

        $this->assertEquals('/additional/test', $this->urlController->pluginUrl('additional', ['param' => 'test']));

        $this->assertEquals('/additional/test', $this->urlController->pluginUrl('additional', ['param' => 'test'], true));
        $this->assertEquals('/additional/hello-world', $this->urlController->pluginUrl('additional', [], true));

        $e = null;
        try {
            $this->urlController->pluginUrl('additional');
        } catch (\Exception $e) { }

        $this->assertInstanceOf('umi\route\exception\RuntimeException', $e);
    }

    public function testAbsoluteURL()
    {
        $this->assertEquals('https://example.com/', $this->urlController->pluginAbsoluteUrl('home'));

        $this->assertEquals('https://example.com/additional/test', $this->urlController->pluginAbsoluteUrl('additional', ['param' => 'test']));

        $this->assertEquals('https://example.com/additional/test', $this->urlController->pluginAbsoluteUrl('additional', ['param' => 'test'], true));
        $this->assertEquals('https://example.com/additional/hello-world', $this->urlController->pluginAbsoluteUrl('additional', [], true));

        $e = null;
        try {
            $this->urlController->pluginAbsoluteUrl('additional');
        } catch (\Exception $e) { }

        $this->assertInstanceOf('umi\route\exception\RuntimeException', $e);
    }
}
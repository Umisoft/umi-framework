<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\hmvc\unit\view;

use umi\hmvc\context\Context;
use umi\hmvc\toolbox\factory\ModelFactory;
use umi\hmvc\toolbox\factory\ViewExtensionFactory;
use umi\hmvc\view\TemplateView;
use utest\hmvc\HMVCTestCase;

/**
 * Class TemplateViewTest
 */
class TemplateViewTest extends HMVCTestCase
{
    /**
     * @var TemplateView $view
     */
    protected $view;

    public function setUpFixtures()
    {
        $this->view = new TemplateView([
            'type' => 'php',
            'directory' => self::DIRECTORY . '/fixture/view',
            'extension' => 'phtml',
            'helpers' => [
                'sum' => function ($a, $b) { return $a+$b; }
            ]
        ]);

        $this->injectViewExtensionFactory();
    }

    public function testRender()
    {
        $str = $this->view->render('sample', ['text' => 'hello world']);

        $this->assertEquals('sample: hello world', $str);
    }

    public function testHelpers()
    {
        $str = $this->view->render('helperCall', ['a' => 2, 'b' => 3]);

        $this->assertEquals('2+3=5', $str);
    }

    public function testContextAndModels()
    {
        $this->view = new TemplateView([
            'type' => 'php',
            'directory' => self::DIRECTORY . '/fixture/view',
            'extension' => 'phtml',
            'helpers' => [
                'mock' => 'utest\hmvc\mock\view\helper\MockContextModelHelper'
            ]
        ]);

        $modelFactory = new ModelFactory([
            'mock' => 'utest\hmvc\mock\model\MockBaseModel'
        ]);
        $this->resolveOptionalDependencies($modelFactory);
        $this->view->setModelFactory($modelFactory);

        $this->injectViewExtensionFactory();

        $context = new Context(
            $this->getComponent([]),
            $this->getRequest('/mock'));
        $this->view->setContext($context);

        $this->assertEquals('URI: /mock. Model: mock', $this->view->render('contextModel', []));
    }

    private function injectViewExtensionFactory()
    {
        $viewExtensionFactory = new ViewExtensionFactory();
        $this->resolveOptionalDependencies($viewExtensionFactory);

        $this->view->setViewExtensionFactory($viewExtensionFactory);
        $this->resolveOptionalDependencies($this->view);
    }
}

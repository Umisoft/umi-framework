<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\engine;

use umi\templating\engine\ITemplateEngine;
use umi\templating\engine\php\PhpTemplateEngine;
use umi\templating\extension\adapter\ExtensionAdapter;
use umi\templating\toolbox\factory\ExtensionFactory;
use umi\templating\toolbox\TemplatingTools;
use utest\TestCase;

/**
 * Тесты PHP шаблонизатора
 */
class PhpTemplateEngineTest extends TestCase
{
    /**
     * @var PhpTemplateEngine $view
     */
    protected $view;

    public function setUpFixtures()
    {
        $this->view = new PhpTemplateEngine([
            ITemplateEngine::OPTION_DIRECTORY => __DIR__ . '/data/php',
            ITemplateEngine::OPTION_EXTENSION => 'phtml',

        ]);

        $this->resolveOptionalDependencies($this->view);
    }

    public function testRender()
    {
        $response = $this->view->render('example', ['var' => 'testVal']);

        $this->assertEquals(
            'Hello world! testVal',
            $response,
            'Ожидается, что контент будет установлен.'
        );
    }

    public function testException()
    {
        $e = null;
        try {
            $this->view->render('wrong', []);
        } catch (\Exception $e) {
        }

        $this->assertNotNull($e, 'Ожидается, что исключение будет брошено.');
        $this->assertNotContains('wrong', ob_get_contents(), 'Ожидается, что буффер будет очищен.');
    }

    public function testHelpers()
    {
        $adapter = new ExtensionAdapter();
        $this->resolveOptionalDependencies($adapter);

        $extensionFactory = new ExtensionFactory();
        $this->resolveOptionalDependencies($extensionFactory);

        $collection = $extensionFactory->createHelperCollection();
        $collection->addHelper('mock', 'utest\templating\mock\helper\MockViewHelper');

        $adapter->addHelperCollection('test', $collection);
        $this->view->setExtensionAdapter($adapter);

        $response = $this->view->render('helper', []);

        $this->assertEquals('Helper: mock', $response, 'Ожидается, что mock будет вызван.');
    }

    public function testPartial()
    {
        $response = $this->view->render('partial', []);

        $this->assertEquals(
            'Partial: Hello world! test',
            $response,
            'Ожидается, что mock будет вызван.'
        );
    }

    /**
     * Возвращает набор инструментов для работы с отображением.
     * @return TemplatingTools
     */
    protected final function getViewTools()
    {
        return $this->getTestToolkit()
            ->getToolbox(TemplatingTools::NAME);
    }
}
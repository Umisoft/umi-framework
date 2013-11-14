<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\toolbox;

use umi\templating\engine\ITemplateEngineFactory;
use umi\templating\toolbox\factory\TemplateEngineFactory;
use utest\templating\TemplatingTestCase;

/**
 * Class TemplatingFactoryTest
 */
class TemplatingFactoryTest extends TemplatingTestCase
{
    /**
     * @var ITemplateEngineFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new TemplateEngineFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateTemplateEngine()
    {
        $engine = $this->factory->createTemplateEngine(ITemplateEngineFactory::PHP_ENGINE);

        $this->assertInstanceOf('umi\templating\engine\ITemplateEngine', $engine);
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\toolbox;

use umi\templating\toolbox\ITemplatingTools;
use umi\templating\toolbox\TemplatingTools;
use utest\TestCase;

/**
 * Tests for ViewToolsTest
 */
class ViewToolsTest extends TestCase
{
    /**
     * @var ITemplatingTools $tools
     */
    protected $tools;

    public function setUpFixtures()
    {
        $this->tools = new TemplatingTools();
        $this->resolveOptionalDependencies($this->tools);
    }

    public function testGetViewFactory()
    {
        $factory = $this->tools->getTemplateEngineFactory();

        $this->assertInstanceOf('umi\templating\engine\ITemplateEngineFactory', $factory);
    }

}
 
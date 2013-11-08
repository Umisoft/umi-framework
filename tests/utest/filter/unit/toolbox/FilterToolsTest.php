<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\toolbox;

use umi\filter\toolbox\FilterTools;
use umi\filter\toolbox\IFilterTools;
use utest\TestCase;

class FilterToolsTest extends TestCase
{
    /**
     * @var IFilterTools $loggerTools
     */
    protected $tools;

    protected function setUpFixtures()
    {
        $this->tools = new FilterTools();
        $this->resolveOptionalDependencies($this->tools);
    }

    public function testArrayConfigLogger()
    {
        $factory = $this->tools->getFilterFactory();
        $this->assertInstanceOf('umi\filter\IFilterFactory', $factory);
    }

}

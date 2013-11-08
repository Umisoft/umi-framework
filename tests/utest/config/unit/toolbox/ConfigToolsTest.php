<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit\toolbox;

use umi\config\exception\OutOfBoundsException;
use umi\config\toolbox\ConfigTools;
use utest\TestCase;

class ConfigToolsTest extends TestCase
{
    /**
     * @var ConfigTools $tools
     */
    private $tools;

    public function setUpFixtures()
    {
        $this->tools = new ConfigTools();

        $this->resolveOptionalDependencies($this->tools);
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongReader()
    {
        $this->tools->type = 'wrong';
        $this->tools->getConfigIO();
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongWriter()
    {
        $this->tools->type = 'wrong';
        $this->tools->getConfigIO();
    }
}
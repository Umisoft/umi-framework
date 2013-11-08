<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rbac\unit\toolbox;

use umi\rbac\toolbox\IRbacTools;
use umi\rbac\toolbox\RbacTools;
use utest\TestCase;

class RbacToolsTest extends TestCase
{
    /**
     * @var IRbacTools $tools
     */
    private $tools;

    public function setUpFixtures()
    {
        $this->tools = new RbacTools();

        $this->resolveOptionalDependencies($this->tools);
    }

    public function testRoleFactory()
    {
        $this->assertInstanceOf('umi\rbac\IRoleFactory', $this->tools->getRoleFactory());
    }
}
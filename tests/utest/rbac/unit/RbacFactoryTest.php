<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rbac\unit;

use umi\rbac\exception\InvalidArgumentException;
use umi\rbac\toolbox\factory\RoleFactory;
use utest\rbac\RbacTestCase;

/**
 * Тесты инструментов работы с Rbac.
 */
class RbacFactoryTest extends RbacTestCase
{
    /**
     * @var RoleFactory $factory инструменты Rbac
     */
    protected $factory;

    public function setUpFixtures()
    {
        $this->factory = new RoleFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    /**
     * Тест базового функционала.
     */
    public function testBasic()
    {
        $role = $this->factory->createRole(
            [
                'post.edit',
                'post.view'
            ]
        );

        $this->assertEquals(
            ['post.edit', 'post.view'],
            $role->getPermissions(),
            'Ожидается, что права доступа будут совпадать с начальными.'
        );
    }

    /**
     * Тест наследования ролей.
     */
    public function testAdvanced()
    {
        $role1 = $this->factory->createRole(['post.edit']);
        $role2 = $this->factory->createRole(['post.view']);

        $role = $this->factory->createRole(['post.delete'], [$role1, $role2]);

        $this->assertEquals(
            ['post.delete', 'post.edit', 'post.view'],
            $role->getPermissions(),
            'Ожидается, что права доступа будут унаследованы.'
        );
    }

    /**
     * @test неверного родительского элемента.
     * @expectedException InvalidArgumentException
     */
    public function wrongParentRole()
    {
        $this->factory->createRole(['post.delete'], ['role1']);
    }

}
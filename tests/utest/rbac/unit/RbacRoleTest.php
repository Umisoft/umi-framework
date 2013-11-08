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
use umi\rbac\IRbacRole;
use umi\rbac\RbacRole;
use utest\TestCase;
use utest\rbac\mock\MockRbacAssert;

/**
 * Тесты ролей Rbac.
 */
class RbacRoleTest extends TestCase
{
    /**
     * @var IRbacRole $role роль
     */
    protected $role;

    public function setUpFixtures()
    {
        $this->role = new RbacRole(['post.edit', 'post.view']);
    }

    public function testBasic()
    {
        $this->assertEquals(
            ['post.edit', 'post.view'],
            $this->role->getPermissions(),
            'Ожидается, что права будут установлены.'
        );
        $this->assertTrue(
            $this->role->hasPermission('post.edit'),
            'Ожидается, что права на редактирование у роли есть.'
        );
        $this->assertFalse($this->role->hasPermission('post.create'), 'Ожидается, что прав создание у роли нет.');
    }

    public function testAdvanced()
    {
        $this->assertTrue(
            $this->role->hasPermission(
                'post.create',
                function ($permission) {
                    return $permission == 'post.create';
                }
            ),
            'Ожидается, что у роли есть динамические права на создание.'
        );

        $this->assertFalse(
            $this->role->hasPermission(
                'post.create',
                function ($permission) {
                    return $permission != 'post.create';
                }
            ),
            'Ожидается, что у роли нет динамических прав на создание.'
        );

        $assertion = new MockRbacAssert('post.delete');

        $this->assertTrue(
            $this->role->hasPermission('post.delete', $assertion),
            'Ожидается, что у роли есть динамические права на удаление..'
        );

        $this->assertFalse(
            $this->role->hasPermission('post.create', $assertion),
            'Ожидается, что у роли нет динамических прав на создание.'
        );
    }

    /**
     * @test исключения, при передаче неверного типа динамического правила.
     * @expectedException InvalidArgumentException
     */
    public function invalidAssertion()
    {
        /** @noinspection PhpParamsInspection */
        $this->role->hasPermission('post.create', true);
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rbac\func;

use umi\rbac\toolbox\RbacTools;
use utest\TestCase;

/**
 * Тесты Rbac политики доступа.

 */
class RbacTest extends TestCase
{
    /**
     * @var RbacTools $tools инструменты Rbac
     */
    protected $tools;

    public function setUpFixtures()
    {
        $this->tools = $this->getTestToolkit()
            ->getToolbox(RbacTools::NAME);
    }

    /**
     * Тесты базовой функциональности Rbac
     */
    public function testBasic()
    {
        $role1 = $this->tools->getRoleFactory()
            ->createRole(['post.edit']);
        $role2 = $this->tools->getRoleFactory()
            ->createRole(['post.view']);

        $role = $this->tools->getRoleFactory()
            ->createRole(['post.delete'], [$role1, $role2]);

        $this->assertTrue(
            $role->hasPermission('post.view'),
            'Ожидается, что права на просмотр у роли есть.'
        );
        $this->assertTrue(
            $role->hasPermission('post.delete'),
            'Ожидается, что права на удаление у роли есть.'
        );
        $this->assertFalse(
            $role->hasPermission('post.create'),
            'Ожидается, что прав на создание у роли нет.'
        );

        $this->assertEquals(
            ['post.delete', 'post.edit', 'post.view'],
            $role->getPermissions(),
            'Ожидается, что права доступа будут унаследованы.'
        );

        $this->assertTrue(
            $role->hasPermission(
                'post.create',
                function ($permission) {
                    return $permission == 'post.create';
                }
            ),
            'Ожидается, что у роли есть динамические права на создание.'
        );
    }
}
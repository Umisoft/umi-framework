<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rbac\mock;

use umi\rbac\IRbacAssert;

/**
 * Mock Rbac assertion class.
 */
class MockRbacAssert implements IRbacAssert
{
    /**
     * @var string $permission разрешение
     */
    protected $permission;

    /**
     * Конструктор.
     * @param string $permission устанавливает разрешение.
     */
    public function __construct($permission)
    {
        $this->permission = $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($permission)
    {
        return $this->permission == $permission;
    }
}
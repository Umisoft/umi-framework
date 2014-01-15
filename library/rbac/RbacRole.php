<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\rbac\exception\InvalidArgumentException;

/**
 * Реализация роли Rbac политики доступа.
 */
class RbacRole implements IRbacRole, ILocalizable
{

    use TLocalizable;

    /**
     * @var array $permissions разрешения
     */
    protected $permissions = [];

    /**
     * Конструктор.
     * @param array $permissions разрешения
     */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($permission, $assertion = null)
    {
        $isAllowed = in_array($permission, $this->permissions);

        if (!$isAllowed && $assertion) {
            if ($assertion instanceof IRbacAssert) {
                $isAllowed = $assertion->isAllowed($permission);
            } elseif (is_callable($assertion)) {
                $isAllowed = $assertion($permission);
            } else {
                throw new InvalidArgumentException($this->translate(
                    'Assertion argument must be callable or instance of IRbacAssert interface.'
                ));
            }
        }

        return $isAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
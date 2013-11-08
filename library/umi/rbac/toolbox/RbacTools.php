<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac\toolbox;

use umi\rbac\IRbacAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для Rbac политики доступа.
 */
class RbacTools implements IRbacTools
{

    use TToolbox;

    /**
     * @var string $rbacRoleFactoryClass класс фабрики ролей Rbac
     */
    public $rbacRoleFactoryClass = 'umi\rbac\toolbox\factory\RoleFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'role',
            $this->rbacRoleFactoryClass,
            ['umi\rbac\IRoleFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IRbacAware) {
            $object->setRoleFactory($this->getRoleFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleFactory()
    {
        return $this->getFactory('role');
    }
}
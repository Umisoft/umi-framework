<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\acl\toolbox\factory;

use umi\acl\exception\UnexpectedValueException;
use umi\acl\IACLFactory;
use umi\acl\manager\IACLManager;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для сущностей ACL.
 */
class ACLFactory implements IACLFactory, IFactory
{
    use TFactory;

    /**
     * @var string $aclManagerClass класс менеджера ACL
     */
    public $aclManagerClass = 'umi\acl\manager\ACLManager';

    /**
     * {@inheritdoc}
     */
    public function createACLManager(array $config = [])
    {
        /**
         * @var IACLManager $aclManager
         */
        $aclManager = $this->getPrototype(
            $this->aclManagerClass,
            ['umi\acl\manager\IACLManager']
        )
            ->createInstance();

        return $this->configureACLManager($aclManager, $config);
    }

    /**
     * Конфигурирует ACL-менеджер.
     * @param IACLManager $aclManager
     * @param array $config
     * @throws UnexpectedValueException при неверно заданной конфигурации
     * @return IACLManager
     */
    protected function configureACLManager(IACLManager $aclManager, array $config)
    {
        if (isset($config[self::OPTION_ROLES])) {
            $this->configureACLRoles($aclManager, $config[self::OPTION_ROLES]);
        }
        if (isset($config[self::OPTION_RESOURCES])) {
            $this->configureACLResources($aclManager, $config[self::OPTION_RESOURCES]);
        }
        if (isset($config[self::OPTION_RULES])) {
            $this->configureACLRules($aclManager, $config[self::OPTION_RULES]);
        }

        return $aclManager;
    }

    /**
     * Конфигурирует роли.
     * @param IACLManager $aclManager
     * @param array $rolesConfig
     * @throws UnexpectedValueException
     */
    private function configureACLRoles(IACLManager $aclManager, $rolesConfig)
    {
        if (!is_array($rolesConfig)) {
            throw new UnexpectedValueException(
                $this->translate(
                    'Roles configuration for ACL Manager should be an array.'
                )
            );
        }

        foreach ($rolesConfig as $roleName => $parentRoles) {

            if (!is_array($parentRoles)) {
                throw new UnexpectedValueException(
                    $this->translate(
                        'Parent roles configuration for role "{role}" should be an array.',
                        ['role' => $roleName]
                    )
                );
            }

            $aclManager->addRole($roleName, $parentRoles);
        }
    }

    /**
     * Конфигурирует ресурсы и операции над ними.
     * @param IACLManager $aclManager
     * @param array $resourcesConfig
     * @throws UnexpectedValueException
     */
    private function configureACLResources(IACLManager $aclManager, $resourcesConfig)
    {
        if (!is_array($resourcesConfig)) {
            throw new UnexpectedValueException(
                $this->translate(
                    'Resources configuration for ACL Manager should be an array.'
                )
            );
        }

        foreach ($resourcesConfig as $resourceName) {
            $aclManager->addResource($resourceName);
        }
    }

    /**
     * Конфигурирует разрешения на операции над ресурсами для ролей.
     * @param IACLManager $aclManager
     * @param array $rulesConfig
     * @throws UnexpectedValueException
     */
    private function configureACLRules(IACLManager $aclManager, $rulesConfig)
    {
        if (!is_array($rulesConfig)) {
            throw new UnexpectedValueException(
                $this->translate(
                    'Rules configuration for ACL Manager should be an array.'
                )
            );
        }

        foreach ($rulesConfig as $roleName => $resources) {

            if (!is_array($resources)) {
                throw new UnexpectedValueException(
                    $this->translate(
                        'Rules for role "{role}" should be an array.',
                        ['role' => $roleName]
                    )
                );
            }

            foreach ($resources as $resourceName => $operations) {
                if (!is_array($operations)) {
                    throw new UnexpectedValueException(
                        $this->translate(
                            'Allowed list of operations for role "{role}" and for resource "{resource}" should be an array.',
                            [
                                'role' => $roleName,
                                'resource' => $resourceName
                            ]
                        )
                    );
                }

                $aclManager->allow($roleName, $resourceName, $operations);
            }
        }
    }
}
 
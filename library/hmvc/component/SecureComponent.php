<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\hmvc\acl\IACLResource;

/**
 * Класс компонента, доступ к которому может контролироваться через ACL.
 */
class SecureComponent extends Component implements IACLResource
{
    const ACL_RESOURCE_PREFIX = 'component.';

    /**
     * {@inheritdoc}
     */
    public function getACLResourceName()
    {
        return self::ACL_RESOURCE_PREFIX . $this->name;
    }

}
 
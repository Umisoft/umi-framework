<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\mock\toolbox;

use umi\authentication\IAuthenticationAware;
use umi\authentication\TAuthenticationAware;
use utest\IMockAware;

class MockAuthentication implements IAuthenticationAware, IMockAware
{

    use TAuthenticationAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_authFactory;
    }
}

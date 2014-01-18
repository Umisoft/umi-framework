<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\toolbox;

use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\TMVCEntityFactoryAware;
use utest\IMockAware;

/**
 * Mock-class для aware интерфейса.
 */
class MockMVCEntityFactoryAware implements IMockAware, IMVCEntityFactoryAware
{
    use TMVCEntityFactoryAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_MVCEntityFactory;
    }
}
 
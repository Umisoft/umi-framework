<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\mock\toolbox;

use umi\templating\extension\helper\IHelperFactoryAware;
use umi\templating\extension\helper\THelperFactoryAware;
use utest\IMockAware;

/**
 * Class MockTemplatingAware
 */
class MockTemplatingHelperAware implements IHelperFactoryAware, IMockAware
{
    use THelperFactoryAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_templatingHelperFactory;
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\route\mock\toolbox;

use umi\route\type\factory\IRouteFactoryAware;
use umi\route\type\factory\TRouteFactoryAware;
use utest\IMockAware;

class MockRouteFactoryAware implements IRouteFactoryAware, IMockAware
{

    use TRouteFactoryAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_routeFactory;
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\IComponentAware;
use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\TComponentResponseAware;
use umi\hmvc\component\TComponentAware;
use umi\route\IRouteAware;
use umi\route\TRouteAware;

/**
 * Class MockComponent
 */
class MockComponent implements IComponent, IRouteAware, IComponentAware, IComponentResponseAware
{
    use TRouteAware;
    use TComponentAware;
    use TComponentResponseAware;

    /**
     * {@inheritdoc}
     */
    public function getChildComponent($name)
    {
        return $this->createHMVCComponent([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->createRouter([]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(IComponentRequest $request)
    {
        return $this->createComponentResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function call($controller, IComponentRequest $request)
    {
        return $this->createComponentResponse();
    }
}
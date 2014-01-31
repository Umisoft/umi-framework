<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\controller\plugin;

use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\dispatcher\http\IComponentResponseFactory;
use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextAware;
use umi\hmvc\controller\IController;
use umi\hmvc\controller\plugin\TURLPlugin;

/**
 * Class ControllerURL
 */
class MockURLController implements IController, IContextAware
{
    use TContextAware;
    use TURLPlugin;

    /**
     * {@inheritdoc}
     */
    public function setHTTPComponentResponse(IComponentResponseFactory $response) {}

    /**
     * {@inheritdoc}
     */
    public function __invoke(IHTTPComponentRequest $request)
    {
        throw new \Exception('It is mock controller. Use specific methods.');
    }

    public function pluginUrl($route, array $params = [], $useRequest = false)
    {
        return $this->getUrl($route, $params, $useRequest);
    }

    public function pluginAbsoluteUrl($route, array $params = [], $useRequest = false)
    {
        return $this->getAbsoluteUrl($route, $params, $useRequest);
    }
}

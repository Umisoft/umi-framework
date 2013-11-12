<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc;

use umi\hmvc\component\Component;
use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\toolbox\HMVCTools;
use umi\hmvc\toolbox\IHMVCTools;
use utest\TestCase;

/**
 * Class HMVCTestCase
 */
class HMVCTestCase extends TestCase
{
    const DIRECTORY = __DIR__;

    /**
     * @param string $url
     * @param array $params
     * @return IComponentRequest
     */
    protected function getRequest($url, array $params = [])
    {
        /**
         * @var HMVCTools $mvcTools
         */
        $mvcTools = $this->getTestToolkit()
            ->getToolbox(HMVCTools::NAME);

        return $mvcTools->getComponentRequestFactory()
            ->createComponentRequest($url)
            ->setRouteParams($params);
    }

    protected function getComponent(array $options)
    {
        $component = new Component($options);
        $this->resolveOptionalDependencies($component);

        return $component;
    }
}
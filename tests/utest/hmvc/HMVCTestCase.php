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
use umi\hmvc\toolbox\factory\ComponentRequestFactory;
use utest\TestCase;

/**
 * Class HMVCTestCase
 */
abstract class HMVCTestCase extends TestCase
{
    const DIRECTORY = __DIR__;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->getTestToolkit()->registerToolboxes([
            require(LIBRARY_PATH . '/hmvc/toolbox/config.php'),
            require(LIBRARY_PATH . '/route/toolbox/config.php'),
            require(LIBRARY_PATH . '/http/toolbox/config.php')
        ]);

        parent::setUp();
    }

    /**
     * @param string $url
     * @param array $params
     * @return IComponentRequest
     */
    protected function getRequest($url, array $params = [])
    {
        $componentRequestFactory = new ComponentRequestFactory();
        $this->resolveOptionalDependencies($componentRequestFactory);

        return $componentRequestFactory
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
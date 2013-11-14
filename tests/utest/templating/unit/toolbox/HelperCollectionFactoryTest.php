<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\toolbox;

use umi\templating\toolbox\factory\ExtensionFactory;
use utest\templating\TemplatingTestCase;

/**
 * Class HelperCollectionFactoryTest
 */
class HelperCollectionFactoryTest extends TemplatingTestCase
{
    /**
     * @var ExtensionFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new ExtensionFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateHelperList()
    {
        $collection = $this->factory->createHelperCollection();
        $this->assertInstanceOf('umi\templating\extension\helper\collection\IHelperCollection', $collection);
    }
}
 
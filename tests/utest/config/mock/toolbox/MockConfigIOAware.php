<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\mock\toolbox;

use umi\config\io\IConfigIOAware;
use umi\config\io\TConfigIOAware;

/**
 * Mock class for config aware trait
 */
class MockConfigIOAware implements IConfigIOAware
{
    use TConfigIOAware;
}
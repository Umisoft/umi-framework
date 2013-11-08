<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

class TestFactory implements IFactory
{

    use TFactory;

    public $option;
}
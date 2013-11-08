<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock\toolbox;

use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\TComponentResponseAware;
use utest\IMockAware;

/**
 * Mock-class для aware интерфейса.
 */
class MockComponentResponseAware implements IMockAware, IComponentResponseAware
{
    use TComponentResponseAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_hmvcComponentResponseFactory;
    }
}
 
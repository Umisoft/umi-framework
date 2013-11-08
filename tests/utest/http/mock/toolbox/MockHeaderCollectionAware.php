<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\mock\toolbox;

use umi\http\response\header\IHeaderCollectionAware;
use umi\http\response\header\THeaderCollectionAware;
use utest\IMockAware;

/**
 * Class MockHeaderCollectionAware
 */
class MockHeaderCollectionAware implements IHeaderCollectionAware, IMockAware
{
    use THeaderCollectionAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_httpResponseHeaderCollectionFactory;
    }
}
 
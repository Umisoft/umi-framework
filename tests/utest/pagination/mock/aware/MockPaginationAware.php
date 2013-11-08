<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\pagination\mock\aware;

use umi\pagination\IPaginationAware;
use umi\pagination\TPaginationAware;
use utest\IMockAware;

class MockPaginationAware implements IPaginationAware, IMockAware
{

    use TPaginationAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_paginationFactory;
    }
}
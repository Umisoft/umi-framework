<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\mock\toolbox;

use umi\session\ISessionAware;
use umi\session\TSessionAware;
use utest\IMockAware;

/**
 * Mock класс Aware интерфейса
 */
class MockSessionAware implements ISessionAware, IMockAware
{

    use TSessionAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_sessionService;
    }
}
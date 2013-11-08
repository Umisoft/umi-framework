<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\mock\toolbox;

use umi\session\ISessionManagerAware;
use umi\session\TSessionManagerAware;
use utest\IMockAware;

/**
 * Mock класс Aware интерфейса
 */
class MockSessionManagerAware implements ISessionManagerAware, IMockAware
{

    use TSessionManagerAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_sessionManager;
    }

}
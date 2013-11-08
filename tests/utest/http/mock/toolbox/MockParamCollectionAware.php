<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\mock\toolbox;

use umi\http\request\param\IParamCollectionAware;
use umi\http\request\param\TParamCollectionAware;
use utest\IMockAware;

/**
 * Мок-класс для тестирования aware интерфейса.
 */
class MockParamCollectionAware implements IParamCollectionAware, IMockAware
{

    use TParamCollectionAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_httpParamCollectionFactory;
    }
}
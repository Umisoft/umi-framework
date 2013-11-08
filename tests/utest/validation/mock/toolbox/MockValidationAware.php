<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\mock\toolbox;

use umi\validation\IValidationAware;
use umi\validation\TValidationAware;
use utest\IMockAware;

/**
 * Mock class for validation aware.
 */
class MockValidationAware implements IValidationAware, IMockAware
{

    use TValidationAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->_validatorFactory;
    }
}

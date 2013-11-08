<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\unit\toolbox;

use utest\AwareTestCase;

class ValidationAwareTest extends AwareTestCase
{

    public function testValidationAware()
    {
        $this->awareClassTest(
            'utest\validation\mock\toolbox\MockValidationAware',
            'umi\validation\exception\RequiredDependencyException',
            'Validator factory is not injected in class "utest\validation\mock\toolbox\MockValidationAware".'
        );
        $this->successfulInjectionTest(
            'utest\validation\mock\toolbox\MockValidationAware',
            'umi\validation\IValidatorFactory'
        );
    }
}
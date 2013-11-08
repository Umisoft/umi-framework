<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox;

use utest\AwareTestCase;

class FormAwareTest extends AwareTestCase
{

    public function testBasic()
    {
        $this->awareClassTest(
            'utest\form\mock\toolbox\MockFormAwareTools',
            'umi\form\exception\RequiredDependencyException',
            'Form entity factory is not injected in class "utest\form\mock\toolbox\MockFormAwareTools".'
        );
    }
}
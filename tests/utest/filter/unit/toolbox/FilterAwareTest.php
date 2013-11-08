<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\toolbox;

use utest\AwareTestCase;

/**
 * Тесты инструментов для фильтрации
 */
class FilterAwareTest extends AwareTestCase
{

    public function testFilterAware()
    {
        $this->awareClassTest(
            'utest\filter\mock\toolbox\MockFilterAware',
            'umi\filter\exception\RequiredDependencyException',
            'Filter factory is not injected in class "utest\filter\mock\toolbox\MockFilterAware".'
        );

    }

}
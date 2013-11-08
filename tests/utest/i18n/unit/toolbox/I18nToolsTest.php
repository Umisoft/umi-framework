<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\i18n\unit\toolbox;

use umi\i18n\toolbox\I18nTools;
use utest\TestCase;

/**
 * Тесты I18nTools
 */
class I18nToolsTest extends TestCase
{

    public function testI18n()
    {

        $i18nTools = new I18nTools();
        $this->resolveOptionalDependencies($i18nTools);

        $this->assertInstanceOf(
            'umi\i18n\translator\ITranslator',
            $i18nTools->getService('umi\i18n\translator\ITranslator', null)
        );

    }
}
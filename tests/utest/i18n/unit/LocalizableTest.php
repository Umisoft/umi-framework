<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\i18n\unit;

use umi\i18n\ILocalizable;
use umi\i18n\LocalesService;
use umi\i18n\TLocalizable;
use umi\i18n\translator\Translator;
use utest\TestCase;

/**
 * Тесты транслятора
 */
class LocalizableTest extends TestCase implements ILocalizable
{

    use TLocalizable;

    /**
     * @var Translator $translator
     */
    protected $translator;

    protected function setUpFixtures()
    {

        $localesService = new LocalesService();
        $localesService->setCurrentLocale('ru');
        $localesService->setDefaultLocale('en');
        $this->translator = new Translator();
        $this->translator->setLocalesService($localesService);

        $this->translator->dictionaries = [
            'utest' => [
                'en' => [
                    'test' => 'utest'
                ]
            ],
            'utest\i18n\unit' => [
                'en' => [
                    'test' => 'utest\i18n\unit'
                ]
            ],
            'utest\i18n' => [
                'en' => [
                    'test' => 'utest\i18n'
                ]
            ]
        ];
    }

    public function testLocalizable()
    {
        $this->assertInstanceOf(
            'utest\i18n\unit\LocalizableTest',
            $this->setTranslator($this->translator),
            'Ожидается, что ILocalizable::setTranslator() вернет себя'
        );

        $this->assertEquals(
            [
                'utest\i18n\unit\LocalizableTest',
                'utest\i18n\unit',
                'utest\i18n',
                'utest',
            ],
            $this->getI18nDictionaries(),
            'Ожидаются названия словарей в соответствии с полным именем класса'
        );

        $this->assertEquals('utest\i18n\unit', $this->translate('test'));
    }

}
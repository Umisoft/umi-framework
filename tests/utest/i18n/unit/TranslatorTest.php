<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\i18n\unit;

use umi\config\entity\Config;
use umi\i18n\LocalesService;
use umi\i18n\translator\Translator;
use utest\i18n\I18nTestCase;

/**
 * Тесты транслятора
 */
class TranslatorTest extends I18nTestCase
{

    /**
     * @var Translator $translator
     */
    protected $translator;

    protected $dictionaries = [
        'parent' => [
            'en' => [
                'test {smth}'          => 'test {smth}',
                'one more test {smth}' => 'one more test {smth}',
            ],
            'ru' => [
                'test {smth}' => 'тест {smth}'
            ]
        ],
        'child'  => [
            'en' => [
                'test {smth}'          => 'child test {smth}',
                'one more test {smth}' => 'one more child test {smth}',
            ],
            'ru' => [
                'test {smth}' => 'дочерний тест {smth}'
            ]
        ]
    ];

    protected function setUpFixtures()
    {

        $localesService = new LocalesService();
        $localesService->setCurrentLocale('ru');
        $localesService->setDefaultLocale('en');
        $this->translator = new Translator();
        $this->translator->setLocalesService($localesService);
    }

    public function testArrayDictionaries()
    {

        $this->translator->dictionaries = $this->dictionaries;
        $this->assertEquals(
            'дочерний тест чего-нибудь',
            $this->translator->translate(['child', 'parent'], 'test {smth}', ['smth' => 'чего-нибудь'], 'ru'),
            'Ожидается, что будет взят лейбл из певого подходящего словаря'
        );

        $this->assertEquals(
            'дочерний тест чего-нибудь',
            $this->translator->translate(['child', 'parent'], 'test {smth}', ['smth' => 'чего-нибудь']),
            'Ожидается, что при неуказанной локали лейбл будет взят для текущей локали'
        );

        $this->assertEquals(
            'one more child test something',
            $this->translator->translate(['child', 'parent'], 'one more test {smth}', ['smth' => 'something']),
            'Ожидается, что при несуществовании лейбла в текущей локали вернется значение из дефолтной локали'
        );

        $this->assertEquals(
            'test something',
            $this->translator->translate(['nonExistentDictionary'], 'test {smth}', ['smth' => 'something']),
            'Ожидается, что при отсутствии заданных словарей в конфигурации транслятора вернется само сообщение'
        );

        $this->assertEquals(
            'non existent label "something"',
            $this->translator->translate(['child', 'parent'], 'non existent label "{smth}"', ['smth' => 'something']),
            'Ожидается, что при отсутствии лейбла вернется само сообщение'
        );
    }

    public function testTranslate()
    {

        $this->translator->dictionaries = new Config($this->dictionaries);
        $this->assertEquals(
            'тест чего-нибудь',
            $this->translator->translate(['parent', 'child'], 'test {smth}', ['smth' => 'чего-нибудь'], 'ru'),
            'Ожидается, что будет взят лейбл из певого подходящего словаря'
        );

        $this->assertEquals(
            'test something',
            $this->translator->translate(['nonExistentDictionary'], 'test {smth}', ['smth' => 'something']),
            'Ожидается, что при отсутствии заданных словарей в конфигурации транслятора вернется само сообщение'
        );
    }

    public function testWrongConfig()
    {
        $this->translator->dictionaries = ['dictionaryName' => 'wrongDictionaryConfig'];

        $this->assertEquals(
            'test',
            $this->translator->translate(['dictionaryName'], 'test'),
            'Ожидается, что при неверном конфиге словарей при переводе вернется само сообщение'
        );

        $this->translator->dictionaries['dictionaryName'] = [];

        $this->assertEquals(
            'test',
            $this->translator->translate(['dictionaryName'], 'test'),
            'Ожидается, что при неверном конфиге словарей при переводе вернется само сообщение'
        );

        $this->translator->dictionaries['dictionaryName']['en'] = 'wrongLocaleId';

        $this->assertEquals(
            'test',
            $this->translator->translate(['dictionaryName'], 'test'),
            'Ожидается, что при неверном конфиге словарей при переводе вернется само сообщение'
        );

        $this->translator->dictionaries = 'wrongDictionariesConfig';
        $e = null;
        try {
            $this->translator->translate(['dictionaryName'], 'test');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\i18n\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение, когда изначально конфиг словарей задан неверно'
        );
    }

}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n\translator;

use umi\i18n\exception\UnexpectedValueException;
use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\log\ILogger;
use umi\log\ILoggerAware;
use umi\log\TLoggerAware;
use umi\spl\config\TConfigSupport;

/**
 * Транслятор.
 */
class Translator implements ITranslator, ILocalesAware, ILoggerAware
{

    use TLocalesAware;
    use TLoggerAware;
    use TConfigSupport;

    /**
     * @var array $dictionaries словари в формате
     * [
     *    'dictionaryName' => [
     *        'localeID' => [
     *            'label' => 'translation',
     *            ...
     *        ],
     *        ...
     *    ],
     *    ...
     * ]

     */
    protected $dictionaries = [];

    /**
     * Конструктор.
     * @param array|\Traversable $dictionaries конфигурация словарей в формате
     * [
     *    'dictionaryName' => [
     *        'localeID' => [
     *            'label' => 'translation',
     *            ...
     *        ],
     *        ...
     *    ],
     *    ...
     * ]
     * @throws UnexpectedValueException в случае неверной конфигурации словарей
     */
    public function __construct($dictionaries)
    {
        try {
            $dictionaries = $this->configToArray($dictionaries);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException('Dictionaries configuration should be an array or Traversable.', 0, $e);
        }
        $this->dictionaries = $dictionaries;
    }

    /**
     * {@inheritdoc}
     */
    public function translate(array $dictionaries, $message, array $placeholders = [], $localeId = null)
    {
        if (!$localeId) {
            $localeId = $this->getCurrentLocale();
        }

        $translation = null;
        foreach ($dictionaries as $dictionaryName) {
            $labels = $this->getLabels($dictionaryName, $localeId);
            if (isset($labels[$message])) {
                $translation = $labels[$message];
                break;
            }
        }

        if (!$translation) {
            $defaultLocaleId = $this->getDefaultLocale();
            if ($localeId != $defaultLocaleId) {
                foreach ($dictionaries as $dictionaryName) {
                    $labels = $this->getLabels($dictionaryName, $defaultLocaleId);
                    if (isset($labels[$message])) {
                        $translation = $labels[$message];
                        break;
                    }
                }
            }
        }
        if (!$translation) {
            $translation = $message;
        }

        $replace = [];
        foreach ($placeholders as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($translation, $replace);
    }

    /**
     * Возвращает список лейблов локали в словаре
     * @param string $dictionaryName
     * @param string $localeId
     * @return array
     */
    protected function getLabels($dictionaryName, $localeId)
    {
        if (!isset($this->dictionaries[$dictionaryName])) {
            return [];
        }
        $dictionary = $this->dictionaries[$dictionaryName];

        try {
            $dictionary = $this->configToArray($dictionary);
        } catch (\InvalidArgumentException $e) {
            $this->log(
                ILogger::LOG_WARNING,
                'Configuration for dictionary "{dictionary}" is incorrect.',
                ['dictionary' => $dictionaryName]
            );

            return [];
        }

        if (!isset($dictionary[$localeId])) {
            $this->log(
                ILogger::LOG_WARNING,
                'Configuration for locale "{locale}" in dictionary "{dictionary}" was not found.',
                ['locale' => $localeId, 'dictionary' => $dictionaryName]
            );

            return [];
        }

        $localeDictionary = $dictionary[$localeId];

        try {
            $localeDictionary = $this->configToArray($localeDictionary);
        } catch (\InvalidArgumentException $e) {
            $this->log(
                ILogger::LOG_WARNING,
                'Configuration for locale "{locale}" in dictionary "{dictionary}" is incorrect.',
                ['locale' => $localeId, 'dictionary' => $dictionaryName]
            );

            return [];
        }

        return $localeDictionary;
    }

}

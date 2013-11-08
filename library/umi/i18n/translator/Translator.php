<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n\translator;

use Traversable;
use umi\i18n\exception\UnexpectedValueException;
use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\log\ILogger;
use umi\log\ILoggerAware;
use umi\log\TLoggerAware;

/**
 * Транслятор.
 */
class Translator implements ITranslator, ILocalesAware, ILoggerAware
{

    use TLocalesAware;
    use TLoggerAware;

    /**
     * @var array|Traversable $dictionaries словари в формате
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
    public $dictionaries = [];

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
        $dictionaries = $this->getDictionariesList();
        if (!isset($dictionaries[$dictionaryName])) {
            return [];
        }
        $dictionary = $dictionaries[$dictionaryName];
        if ($dictionary instanceof Traversable) {
            $dictionary = iterator_to_array($dictionary, true);
        }
        if (!is_array($dictionary)) {
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
        if ($localeDictionary instanceof Traversable) {
            $localeDictionary = iterator_to_array($localeDictionary, true);
        }
        if (!is_array($localeDictionary)) {
            $this->log(
                ILogger::LOG_WARNING,
                'Configuration for locale "{locale}" in dictionary "{dictionary}" is incorrect.',
                ['locale' => $localeId, 'dictionary' => $dictionaryName]
            );

            return [];
        }

        return $localeDictionary;
    }

    /**
     * Возвращает список используемых словарей
     * @throws UnexpectedValueException
     * @return array
     */
    protected function getDictionariesList()
    {
        if ($this->dictionaries instanceof Traversable) {
            $this->dictionaries = iterator_to_array($this->dictionaries, true);
        }
        if (!is_array($this->dictionaries)) {
            throw new UnexpectedValueException('Dictionaries configuration should be an array or Traversable.');
        }

        return $this->dictionaries;
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\i18n\translator\ITranslator;

/**
 * Помощник шаблонов для перевода сообщений.
 */
class TranslateHelper implements ILocalizable
{
    /**
     * Словарь по умолчанию
     */
    const DICTIONARY_DEFAULT = 'application';

    /**
     * @var ITranslator $translator транслятор
     */
    private $translator;

    /**
     * {@inheritdoc}
     */
    public function setTranslator(ITranslator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * Переводит сообщение.
     * @param string $message сообщение
     * @param array $placeholders список плейсхолдеров
     * @param array $dictionaries
     * @return string
     */
    public function __invoke($message, array $placeholders = [], array $dictionaries = [])
    {
        $dictionaries = $dictionaries ?: [self::DICTIONARY_DEFAULT];

        if ($this->translator) {
            return $this->translator->translate($dictionaries, $message, $placeholders);
        }
        $replace = [];
        foreach ($placeholders as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }
}
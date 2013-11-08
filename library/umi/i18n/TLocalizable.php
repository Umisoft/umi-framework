<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n;

use umi\i18n\translator\ITranslator;

/**
 * Трейт для поддержки локализации.
 */
trait TLocalizable
{
    /**
     * @var ITranslator $_translator транслятор
     */
    private $_translator;

    /**
     * Устанавливает транслятор для локализации
     * @param ITranslator $translator транслятор
     * @return $this
     */
    public function setTranslator(ITranslator $translator)
    {
        $this->_translator = $translator;

        return $this;
    }

    /**
     * Возвращает список имен словарей в которых будет производиться поиск перевода сообщений и лейблов
     * данного компонента. Приоритет поиска соответсвует последовательности словарей в списке.
     * @return array
     */
    public function getI18nDictionaries()
    {
        $classParts = explode('\\', __CLASS__);

        $dictionaries = [];
        for ($i = count($classParts); $i > 0; $i--) {
            $dictionaries[] = implode('\\', array_slice($classParts, 0, $i));
        }

        return $dictionaries;
    }

    /**
     * Возвращает сообщение из указанного словаря, переведенное для текущей или указанной локали.
     * Текст сообщения может содержать плейсхолдеры. Ex: File "{path}" not found
     * Если идентификатор локали не указан, будет использована текущая локаль.
     * @param string $message текст сообщения на языке разработки
     * @param array $placeholders значения плейсхолдеров для сообщения. Ex: array('{path}' => '/path/to/file')
     * @param string $localeId идентификатор локали в которую осуществляется перевод (ru, en_us)
     * @return string
     */
    protected function translate($message, array $placeholders = [], $localeId = null)
    {
        $dictionaries = $this->getI18nDictionaries();
        if ($this->_translator) {
            return $this->_translator->translate($dictionaries, $message, $placeholders, $localeId);
        }
        $replace = [];
        foreach ($placeholders as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }
}

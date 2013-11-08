<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n\translator;

/**
 * Интерфейс транслятора.
 */
interface ITranslator
{
    /**
     * Возвращает сообщение из указанного словаря, переведенное для текущей или указанной локали
     * @param array $dictionaries возможные словари для перевода
     * @param string $message текст сообщения на языке разработки.
     * Может содержать плейсхолдеры. Ex: File "{path}" not found
     * @param array $placeholders значения плейсхолдеров для сообщения. Ex: array('path' => '/path/to/file')
     * @param string $localeId идентификатор локали в которую осуществляется перевод (ru, en_us).
     * Если не указан, будет использована текущая локаль.
     * @return string
     */
    public function translate(array $dictionaries, $message, array $placeholders = [], $localeId = null);
}

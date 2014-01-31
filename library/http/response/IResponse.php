<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\response;

use umi\http\response\header\IHeaderCollection;

/**
 * Интерфейс для работы с HTTP ответом.
 */
interface IResponse
{
    /**
     * Тип ответа - JSON
     */
    const TYPE_JSON = 'application/json';
    /**
     * Тип ответа - XML
     */
    const TYPE_XML = 'application/xml';
    /**
     * Код ответа - успешно.
     */
    const SUCCESS = 200;
    /**
     * Код ответа - не найдено.
     */
    const NOT_FOUND = 404;
    /**
     * Код ответа - переадресация.
     */
    const REDIRECT_PERMANENT = 301;

    /**
     * Возвращает коллекцию заголовков ответа.
     * @return IHeaderCollection
     */
    public function getHeaders();

    /**
     * Выставляет коллекцию заголовков ответа.
     * @param IHeaderCollection $headers
     * @return self
     */
    public function setHeaders(IHeaderCollection $headers);

    /**
     * Устанавливает данные ответа.
     * @param mixed $data
     * @return self
     */
    public function setContent($data);

    /**
     * Возвращает данные ответа.
     * @return mixed
     */
    public function getContent();

    /**
     * Возвращает код ответа.
     * @return int
     */
    public function getCode();

    /**
     * Устанавливает код ответа.
     * @param int $code
     * @return self
     */
    public function setCode($code);

    /**
     * Отправляет данные на вывод.
     */
    public function send();
}
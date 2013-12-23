<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request;

use umi\http\request\param\IParamCollection;

/**
 * Интерфейс для работы с HTTP запросом.
 */
interface IRequest
{
    /** CLI метод запроса */
    const METHOD_CLI = 'cli';
    /** HTTP метод запроса - GET */
    const METHOD_GET = 'get';
    /** HTTP метод запроса - POST */
    const METHOD_POST = 'post';
    /** HTTP метод запроса - PUT */
    const METHOD_PUT = 'put';
    /** HTTP метод запроса - DELETE */
    const METHOD_DELETE = 'delete';
    /** HTTP метод запроса - HEAD */
    const METHOD_HEAD = 'head';

    /** HTTP контейнер - SERVER */
    const HEADERS = 'header';
    /** HTTP контейнер - GET */
    const GET = 'get';
    /** HTTP контейнер - POST */
    const POST = 'post';
    /** HTTP контейнер - COOKIE */
    const COOKIE = 'cookie';
    /** HTTP контейнер - FILES */
    const FILES = 'files';

    /**
     * Возвращает метод запроса.
     * @return string
     */
    public function getMethod();

    /**
     * Возвращает протокол запроса.
     * @return string
     */
    public function getScheme();

    /**
     * Возвращает имя хоста в запросе.
     * @return string
     */
    public function getHost();

    /**
     * Возвращает URI текущего сервера
     * @return string
     */
    public function getHostURI();

    /**
     * Возвращает версию протокола запроса.
     * @return int
     */
    public function getVersion();

    /**
     * Возвращает содержимое запроса в RAW виде.
     * @return string
     */
    public function getContent();

    /**
     * Возвращает запрошеный URI.
     * @return string
     */
    public function getRequestURI();

    /**
     * Возвращает реферера запроса.
     * Если реферер является внутренним для данного домена,
     * то он будет относительным.
     * @return string
     */
    public function getReferer();

    /**
     * Возвращает переменную из параметров.
     * @param string $container имя контейнера параметров
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @return mixed значение из GET
     */
    public function getVar($container, $name, $default = null);

    /**
     * Возвращает контейнер GET параметров.
     * @param string $container имя контейнера параметров
     * @return IParamCollection коллекция параметров
     */
    public function getParams($container);
}
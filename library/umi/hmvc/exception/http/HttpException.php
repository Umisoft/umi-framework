<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\exception\http;

use umi\hmvc\exception\IException;

/**
 * HTTP исключения.
 */
class HttpException extends \Exception implements IException
{
    /** 400 - Bad Request */
    const HTTP_BAD_REQUEST = 400;
    /** 401 - Unauthorized */
    const HTTP_UNAUTHORIZED = 401;
    /** 402 - Payment required */
    const HTTP_PAYMENT_REQUIRED = 402;
    /** 403 - Forbidden */
    const HTTP_FORBIDDEN = 403;
    /** 404 - Page Not Found */
    const HTTP_NOT_FOUND = 404;
    /** 405 - Method Not Allowed */
    const HTTP_METHOD_NOT_ALLOWED = 405;
    /** 406 - Not Acceptable */
    const HTTP_NOT_ACCEPTABLE = 406;
    /** 407 - Proxy Authentication Required */
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    /** 408 - Request Timeout */
    const HTTP_REQUEST_TIMEOUT = 408;
    /** 409 - Conflict */
    const HTTP_CONFLICT = 409;
    /** 410 - Gone */
    const HTTP_GONE = 410;
    /** 411 - Length Required */
    const HTTP_LENGTH_REQUIRED = 411;
    /** 412 - Precondition Failed */
    const HTTP_PRECONDITION_FAILED = 412;
    /** 413 - Request Entity Too Large */
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    /** 414 - Request-URI Too Large */
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    /** 415 - Unsupported Media Type */
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    /** 416 - Requested Range Not Satisfiable */
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    /** 417 - Expectation Failed */
    const HTTP_EXPECTATION_FAILED = 417;
    /** 422 - Unprocessable Entity */
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    /** 423 - Locked */
    const HTTP_LOCKED = 423;
    /** 424 - Failed Dependency */
    const HTTP_FAILED_DEPENDENCY = 424;
    /** 426 - Upgrade Required */
    const HTTP_UPGRADE_REQUIRED = 426;

    /** 500 - Internal server error */
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    /** 501 - Not implemented */
    const HTTP_NOT_IMPLEMENTED = 501;
    /** 502 - Bad gateway */
    const HTTP_BAD_GATEWAY = 502;
    /** 503 - Service unavailable */
    const HTTP_SERVICE_UNAVAILABLE = 503;
    /** 504 - Gateway timeout */
    const HTTP_GATEWAY_TIMEOUT = 504;
    /** 505 - Version not supported */
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    /** 507 - Insufficient Storage */
    const HTTP_INSUFFICIENT_STORAGE = 507;
    /** 508 - Loop Detected */
    const HTTP_LOOP_DETECTED = 508;
    /** 509 - Bandwidth Limit Exceeded  */
    const HTTP_BANDWIDTH_LIMIT_EXCEEDED = 509;

    /**
     * {@inheritdoc}
     */
    public function __construct($code, $message, \Exception $prev = null)
    {
        parent::__construct($message, $code, $prev);
    }
}
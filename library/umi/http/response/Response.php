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
use umi\http\response\header\IHeaderCollectionAware;
use umi\http\response\header\THeaderCollectionAware;

/**
 * Реализация HTTP ответа сервера.
 */
class Response implements IResponse, IHeaderCollectionAware
{
    use THeaderCollectionAware;

    /**
     * @var string $content данные ответа
     */
    protected $content;
    /**
     * @var int $code HTTP код ответа
     */
    protected $code = 200;
    /**
     * @var IHeaderCollection $headers
     */
    private $headers;

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        if (!$this->headers) {
            return $this->headers = $this->createHttpHeaderCollection();
        }

        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($data)
    {
        $this->content = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = (int) $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $oldResponseCode = http_response_code() ? : self::SUCCESS;

        try {
            http_response_code($this->code);
            $this->getHeaders()
                ->send();
            echo $this->getContent();
        } catch (\Exception $e) {
            http_response_code($oldResponseCode);
            throw $e;
        }
    }
}
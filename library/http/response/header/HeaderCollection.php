<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\response\header;

/**
 * Класс для заголовков HTTP ответа.
 */
class HeaderCollection implements IHeaderCollection
{
    /**
     * @var array $headers заголовки ответа
     */
    protected $headers = [];
    /**
     * @var array $cookies cookies cookies
     */
    protected $cookies = [];

    /**
     * {@inheritdoc}
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookie($name, $value, $options = [])
    {
        $this->cookies[$name] = $this->prepareCookie($value, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    public function send()
    {
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        foreach ($this->cookies as $name => $data) {
            setcookie(
                $name,
                $data['value'],
                $data['expire'],
                $data['path'],
                $data['domain'],
                $data['secure'],
                $data['httponly']
            );
        }
    }

    /**
     * Подготавливает опции cookie.
     * @param string $value значение
     * @param array $options опции
     * @return array подготовленные опции
     */
    private function prepareCookie($value, $options)
    {
        return [
            'value'    => $value,
            'expire'   => isset($options['expire']) ? $options['expire'] : 0,
            'path'     => isset($options['path']) ? $options['path'] : null,
            'domain'   => isset($options['domain']) ? $options['domain'] : null,
            'secure'   => isset($options['secure']) ? $options['secure'] : false,
            'httponly' => isset($options['httponly']) ? $options['httponly'] : false
        ];
    }
}

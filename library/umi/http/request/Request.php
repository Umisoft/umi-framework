<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request;

use umi\http\exception\RuntimeException;
use umi\http\request\param\IParamCollection;
use umi\http\request\param\ParamCollection;

/**
 * Компонент работы с HTTP запросом.
 */
class Request implements IRequest
{

    /**
     * @var IParamCollection[] $paramCollectionInstances экземпляры контейнеров
     */
    protected $paramCollectionInstances = [];

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return strtolower($this->getVar(self::HEADERS, 'REQUEST_METHOD', 'cli'));
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        if ($this->getVar(self::HEADERS, "HTTPS") !== null) {
            return 'https';
        }

        $protocol = strtolower($this->getVar(self::HEADERS, "SERVER_PROTOCOL", 'cli'));
        list($protocol) = explode('/', $protocol);

        return $protocol;
    }

    /**
     * Возвращает имя хоста HTTP запроса.
     * @return string
     */
    public function getHost()
    {
        return $this->getVar(IRequest::HEADERS, 'HTTP_HOST');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        $protocol = $this->getVar(self::HEADERS, "SERVER_PROTOCOL", 'CLI');
        $serverProtocol = explode('/', $protocol);

        return isset($serverProtocol[1]) ? $serverProtocol : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return file_get_contents("php://input");
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestUri()
    {
        return $this->getVar(self::HEADERS, 'REQUEST_URI', '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getReferer()
    {
        $referer = $this->getVar(self::HEADERS, 'HTTP_REFERER');
        $host = strtolower($this->getScheme() . '://' . $this->getHost());
        $refererHost = strtolower(substr($referer, 0, strlen($host)));

        return ($host == $refererHost) ? substr($referer, strlen($host)) : $referer;
    }

    /**
     * {@inheritdoc}
     */
    public function getVar($container, $name, $default = null)
    {
        return $this->getParams($container)
            ->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getParams($container)
    {
        if (!isset($this->paramCollectionInstances[$container])) {
            $this->paramCollectionInstances[$container] = $this->createContainer($container);
        }

        return $this->paramCollectionInstances[$container];
    }

    /**
     * Возвращает коллекцию параметров определенного типа.
     * todo: мб в фабрику коллекции параметров?
     * @param string $container тип контейнера
     * @return IParamCollection коллекция параметров выбранного типа
     * @throws RuntimeException если тип не задан в контейнере классов
     */
    protected function createContainer($container)
    {
        switch ($container) {
            case self::GET:
                $params = & $_GET;
                break;
            case self::POST:
                $params = & $_POST;
                break;
            case self::COOKIE:
                $params = & $_COOKIE;
                break;
            case self::FILES:
                $params = & $_FILES;
                break;
            case self::HEADERS:
                $params = & $_SERVER;
                break;
            default:
                $params = [];
        }

        return new ParamCollection($params);
    }
}

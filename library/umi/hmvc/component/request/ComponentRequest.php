<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\request;

use umi\http\request\Request;

/**
 * HTTP запрос компонента.
 */
class ComponentRequest extends Request implements IComponentRequest
{
    /**
     * @var string $uri URI
     */
    private $uri;

    /**
     * Конструктор.
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteParams(array $params)
    {
        $this->getParams(self::ROUTE)
            ->setArray($params);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestUri()
    {
        return $this->uri;
    }
}
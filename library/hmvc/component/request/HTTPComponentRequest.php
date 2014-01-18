<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\request;

use umi\hmvc\component\IComponent;
use umi\http\request\Request;

/**
 * HTTP запрос компонента.
 */
class HTTPComponentRequest extends Request implements IHTTPComponentRequest
{
    /**
     * @var IComponent $component
     */
    private $component;

    /**
     * Конструктор.
     * @param IComponent $component
     */
    public function __construct(IComponent $component)
    {
        $this->component = $component;
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
    public function getComponent()
    {
        return $this->component;
    }

}

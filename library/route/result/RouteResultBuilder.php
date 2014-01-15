<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\result;

/**
 * Билдер результата маршрутизации.
 */
class RouteResultBuilder implements IRouteResultBuilder
{

    /**
     * @var array $names массив имен совпавших маршрутов.
     */
    protected $names = [];
    /**
     * @var array $params массив совпавших параметров.
     */
    protected $params = [];
    /**
     * @var string $matchedPart совпавшая часть URL.
     */
    protected $matchedPart;
    /**
     * @var string $unmatchedPart несовпавшая часть URL.
     */
    protected $unmatchedPart;

    /**
     * {@inheritdoc}
     */
    public function addMatch($name, array $params, $matchedPart)
    {
        $this->names[] = $name;
        $this->params = $params + $this->params;
        $this->matchedPart .= $matchedPart;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return new RouteResult(implode('/', $this->names), $this->params, $this->matchedPart, $this->unmatchedPart);
    }

    /**
     * {@inheritdoc}
     */
    public function setUnmatchedUrl($unmatchedPart)
    {
        $this->unmatchedPart = $unmatchedPart;

        return $this;
    }
}
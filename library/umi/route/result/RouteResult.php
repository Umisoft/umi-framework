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
 * Результат маршрутизации.
 */
class RouteResult implements IRouteResult
{
    /**
     * @var string $route имя маршрута
     */
    protected $route;
    /**
     * @var array $matches массив совпадений(параметров)
     */
    protected $matches;
    /**
     * @var string $matchedUrl совпавшая часть URL
     */
    protected $matchedUrl;
    /**
     * @var string $unmatchedUrl несовпавшая(нетронутая) часть URL
     */
    protected $unmatchedUrl;

    /**
     * Конструктор.
     * @param string $route имя маршрута
     * @param array $matches массив совпадений
     * @param string $matchedUrl совпавшая часть URL
     * @param string $unmatchedUrl несовпавшая часть URL
     */
    public function __construct($route, array $matches, $matchedUrl, $unmatchedUrl)
    {
        $this->route = $route;
        $this->matches = $matches;
        $this->matchedUrl = $matchedUrl;
        $this->unmatchedUrl = $unmatchedUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchedUrl()
    {
        return $this->matchedUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnmatchedUrl()
    {
        return $this->unmatchedUrl;
    }
}
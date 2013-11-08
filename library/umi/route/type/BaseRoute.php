<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type;

/**
 * Абстрактный базовый класс для правил маршрутизатора.
 */
abstract class BaseRoute implements IRoute
{
    /**
     * @var string $route правило маршрутизатора
     */
    public $route;
    /**
     * @var array $defaults параметры по-умолчанию
     */
    public $defaults = [];
    /**
     * @var IRoute[] $subroutes дочерние правила маршрутизатора
     */
    protected $subroutes = [];
    /**
     * @var array $params параметры, полученные при разборе
     */
    protected $params = [];

    /**
     * Конструктор.
     * @param IRoute[] $subroutes дочерние правила маршрутизатора
     */
    public function __construct(array $subroutes = [])
    {
        $this->subroutes = $subroutes;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        return $this->params + $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubRoutes()
    {
        return $this->subroutes;
    }
}
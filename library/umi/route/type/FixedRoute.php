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
 * Статическое правило маршрутизатора.
 * @example user/profile
 */
class FixedRoute extends BaseRoute implements IRoute
{

    /**
     * {@inheritdoc}
     */
    public function match($url)
    {
        return (!$this->route || strpos($url, $this->route) === 0) ? strlen($this->route) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function assemble(array $params = [])
    {
        return $this->route;
    }
}
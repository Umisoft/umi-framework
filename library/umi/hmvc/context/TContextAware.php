<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

/**
 * Трейт для поддержки внедрения контекстно зависимых объектов.
 * {@internal}
 */
trait TContextAware
{
    /**
     * @var IContext $_componentContext
     */
    private $_componentContext;

    /**
     * Устанавливает контекст работы компонента.
     * @param IContext $context
     */
    public function setContext(IContext $context)
    {
        $this->_componentContext = $context;
    }

    /**
     * Очищает установленный контекст.
     */
    public function clearContext()
    {
        $this->_componentContext = null;
    }

    protected function hasContext()
    {
        return (bool) $this->_componentContext;
    }

    protected function getContext()
    {
        return $this->_componentContext;
    }
}

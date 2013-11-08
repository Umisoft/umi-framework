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
    use TComponentContext;
    use TRequestContext;

    /**
     * Очищает установленный контекст.
     * @return $this
     */
    public function clearContext()
    {
        $this->_contextComponent = null;
        $this->_contextRequest = null;

        return $this;
    }
}
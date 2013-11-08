<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для поддержки внедрения запроса из контекста.
 */
trait TRequestContext
{
    /**
     * @var IComponentRequest $_contextRequest запрос
     */
    private $_contextRequest;

    /**
     * Устанавливает контекстно-зависимый запрос.
     * @param IComponentRequest $request
     */
    public function setContextRequest(IComponentRequest $request = null)
    {
        $this->_contextRequest = $request;
    }

    /**
     * Проверяет доступность контекстно зависимого запроса.
     * @return bool
     */
    protected function hasContextRequest()
    {
        return !is_null($this->_contextRequest);
    }

    /**
     * Возвращает контекстно-зависимый запрос.
     * @return IComponentRequest
     * @throws RequiredDependencyException если контекст не был внедрен
     */
    protected function getContextRequest()
    {
        if (!$this->_contextRequest) {
            throw new RequiredDependencyException(sprintf(
                'Context request has not injected.'
            ));
        }

        return $this->_contextRequest;
    }
}
 
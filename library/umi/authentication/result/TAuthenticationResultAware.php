<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\result;

use umi\authentication\adapter\IAuthAdapter;
use umi\authentication\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки создания результатов аутентификации.
 * @internal
 */
trait TAuthenticationResultAware
{
    /**
     * @var IAuthenticationResultFactory $_authResultFactory
     */
    private $_authResultFactory;

    /**
     * Устанавливает инструменты аутентификации.
     * @param IAuthenticationResultFactory $authResultFactory инструменты
     * @return self
     */
    public final function setAuthenticationResultFactory(IAuthenticationResultFactory $authResultFactory)
    {
        $this->_authResultFactory = $authResultFactory;
    }

    /**
     * Возвращает объект результата авторизации.
     * @param int $status статус авторизации
     * @param mixed|null $identity ресурс, полученый в результате авторизации
     * @return IAuthAdapter
     * @throws RequiredDependencyException если инструменты для работы с аутентификацией не установлены
     */
    protected final function createAuthResult($status, $identity = null)
    {
        if (!$this->_authResultFactory) {
            throw new RequiredDependencyException(sprintf(
                'Authentication result factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_authResultFactory->createResult($status, $identity);
    }
}
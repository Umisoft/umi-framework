<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\result;

/**
 * Результат авторизации.
 * Данный результат является результатом работы адаптера по авторизации.
 */
class AuthResult implements IAuthResult
{
    /**
     * @var int $status статус авторизации
     */
    protected $status;
    /**
     * @var mixed $identity ресурс, полученные в результате авторизации
     */
    protected $identity;

    /**
     * Конструктор.
     * @param int $status статус авторизации
     * @param mixed $identity ресурс, полученные в результате авторизации
     */
    public function __construct($status, $identity = null)
    {
        $this->status = $status;
        $this->identity = $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->status < 0x00FF;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return $this->identity;
    }
}
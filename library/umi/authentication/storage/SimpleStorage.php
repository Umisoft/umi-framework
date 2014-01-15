<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\storage;

/**
 * Класс простого хранилища для аутентификации, запоминает характеристики субъекта аутентификации в памяти.
 */
class SimpleStorage implements IAuthStorage
{
    /**
     * @var mixed $identity идентификатор
     */
    protected $identity = null;

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIdentity()
    {
        return $this->identity !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function clearIdentity()
    {
        $this->identity = null;

        return $this;
    }
}
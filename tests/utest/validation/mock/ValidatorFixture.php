<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\mock;

use umi\validation\IValidator;

/**
 * Класс ValidatorFixture
 */
class ValidatorFixture implements IValidator
{

    /**
     * @var array $options опции валидатора
     */
    protected $options;
    /**
     * @var array $messages сообщения об ошибках
     */
    protected $messages = [];
    /**
     * @var bool $isValid "валидность" валидатора
     */
    protected $isValid;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($var)
    {
        $this->messages = [];

        if (isset($this->options['is_valid'])) {
            if (!$this->options['is_valid']) {
                $this->messages[] = "Invalid validator";

                return false;
            } else {
                return $this->options['is_valid'];
            }

        } else {
            throw new \RuntimeException("No 'is_valid' option");
        }
    }
}
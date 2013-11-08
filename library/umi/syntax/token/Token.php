<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax\token;

/**
 * Класс токена(лексема или символ языка).
 */
class Token implements IToken
{
    /**
     * @var int $type тип токена
     */
    protected $type;
    /**
     * @var string $name имя токена
     */
    protected $name;
    /**
     * @var string $value значение токена
     */
    protected $value;

    /**
     * Конструктор.
     * @param int $type тип токена
     * @param string $name имя токена
     * @param string $value значение токена
     */
    public function __construct($type, $name, $value)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
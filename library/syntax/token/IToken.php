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
 * Токен(лексема или символ языка).
 */
interface IToken
{
    /**
     * Терминальный символ.
     */
    const TERMINAL = 0x01;
    /**
     * Нетерминальный символ.
     */
    const NONTERMINAL = 0x02;

    /**
     * Возвращает тип токена.
     * @return int
     */
    public function getType();

    /**
     * Возвращает имя токена.
     * @return string
     */
    public function getName();

    /**
     * Возвращает значение токена.
     * @return string
     */
    public function getValue();
}
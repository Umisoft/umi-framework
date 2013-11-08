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
 * Интерфейс фабрики токенов.
 */
interface ITokenFactory
{
    /**
     * Создает терминальный символ.
     * @param string $name имя токена
     * @param string $value значение токена
     * @return IToken
     */
    public function createTerminal($name, $value);

    /**
     * Создает нетерминальный символ.
     * @param string $name имя токена
     * @param string $value значение токена
     * @return IToken
     */
    public function createNonterminal($name, $value);
}
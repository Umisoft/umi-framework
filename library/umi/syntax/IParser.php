<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax;

use umi\syntax\exception\InvalidGrammarException;
use umi\syntax\exception\SyntaxException;
use umi\syntax\token\IToken;

/**
 * Парсер. Производит синтаксический анализ лексем языка.
 */
interface IParser
{

    /**
     * Производит синтаксический анализ лексем.
     * @param IToken[] $tokens токены(лексемы)
     * @throws SyntaxException если встретился неожиданный конец выражения
     * @throws InvalidGrammarException если парсинг не завершился
     * @return IToken
     */
    public function parse(array $tokens);
}
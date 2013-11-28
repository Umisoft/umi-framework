<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax\toolbox\factory;

use umi\syntax\token\IToken;
use umi\syntax\token\ITokenFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика символов грамматики.
 */
class TokenFactory implements ITokenFactory, IFactory
{

    use TFactory;

    /**
     * @var string $tokenClass класс токена(лексемы)
     */
    public $tokenClass = 'umi\syntax\token\Token';

    /**
     * {@inheritdoc}
     */
    public function createTerminal($name, $value)
    {
        return $this->createToken(IToken::TERMINAL, $name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createNonterminal($name, $value)
    {
        return $this->createToken(IToken::NONTERMINAL, $name, $value);
    }

    /**
     * Создает токен заданного типа.
     * @param string $type тип токена
     * @param string $name имя токена
     * @param string $value значение токена
     * @return IToken
     */
    protected function createToken($type, $name, $value)
    {
        return $this->getPrototype(
                $this->tokenClass,
                ['umi\syntax\token\IToken']
            )
            ->createInstance([$type, $name, $value]);
    }
}

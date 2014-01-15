<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax\token;

use umi\syntax\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки работы с токенами.
 */
trait TTokenAware
{
    /**
     * @var ITokenFactory $_syntaxTokenFactory фабрика токенов
     */
    private $_syntaxTokenFactory;

    /**
     * Устанавливает фабрику токенов.
     * @param ITokenFactory $tokenFactory фабрика
     */
    public final function setSyntaxTokenFactory(ITokenFactory $tokenFactory)
    {
        $this->_syntaxTokenFactory = $tokenFactory;
    }

    /**
     * Создает нетерминальный символ.
     * @param string $name имя
     * @param string $value значение
     * @throws RequiredDependencyException если зависимость не внедрена
     * @return IToken созданный токен
     */
    protected final function createSyntaxNonterminal($name, $value)
    {
        return $this->getSyntaxTokenFactory()
            ->createNonterminal($name, $value);
    }

    /**
     * Создает терминальный символ.
     * @param string $name имя
     * @param string $value значение
     * @throws RequiredDependencyException если зависимость не внедрена
     * @return string созданный токен
     */
    protected final function createSyntaxTerminal($name, $value)
    {
        return $this->getSyntaxTokenFactory()
            ->createTerminal($name, $value);
    }

    /**
     * Возвращает фабрику токенов.
     * @return ITokenFactory
     * @throws RequiredDependencyException
     */
    private final function getSyntaxTokenFactory()
    {
        if (!$this->_syntaxTokenFactory) {
            throw new RequiredDependencyException(sprintf(
                'Syntax token factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_syntaxTokenFactory;
    }
}
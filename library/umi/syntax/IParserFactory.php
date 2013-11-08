<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax;

/**
 * Интерфейс фабрики для создания парсера.
 */
interface IParserFactory
{
    /**
     * Создает экземпляр парсера с заданной грамматикой.
     * @param array $grammar грамматика языка
     * @param array $rules правила грамматики
     * @return IParser
     */
    public function createParser(array $grammar, array $rules);
}
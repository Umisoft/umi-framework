<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax\toolbox;

use umi\syntax\IParserFactory;
use umi\syntax\token\ITokenFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов для работы с синтаксисом.
 */
interface ISyntaxTools extends IToolbox
{

    /**
     * Возвращает фабрику токенов.
     * @return ITokenFactory
     */
    public function getTokenFactory();

    /**
     * Возвращает фабрику парсеров.
     * @return IParserFactory
     */
    public function getParserFactory();
}
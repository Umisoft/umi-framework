<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax\toolbox\factory;

use umi\syntax\IParserFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания парсера.
 */
class ParserFactory implements IParserFactory, IFactory
{

    use TFactory;

    /**
     * @var string $parserClass класс парсера синтаксиса
     */
    public $parserClass = 'umi\syntax\Parser';

    /**
     * {@inheritdoc}
     */
    public function createParser(array $grammar, array $rules)
    {
        return $this->createInstance(
            $this->parserClass,
            [$grammar, $rules],
            ['umi\syntax\IParser']
        );
    }
}

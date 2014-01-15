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
use umi\syntax\ISyntaxAware;
use umi\syntax\token\ITokenAware;
use umi\syntax\token\ITokenFactory;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с синтакисом.
 */
class SyntaxTools implements IToolbox
{
    /**
     * Имя набора инструментов.
     */
    const NAME = 'syntax';

    use TToolbox;

    /**
     * @var string $parserFactoryClass класс фабрики парсеров
     */
    public $parserFactoryClass = '';
    /**
     * @var string $tokenFactoryClass класс фабрики токенов
     */
    public $tokenFactoryClass = '';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'parser',
            $this->parserFactoryClass,
            ['umi\syntax\IParserFactory']
        );

        $this->registerFactory(
            'token',
            $this->tokenFactoryClass,
            ['umi\syntax\token\ITokenFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof ISyntaxAware) {
            $object->setSyntaxParserFactory($this->getParserFactory());
        }

        if ($object instanceof ITokenAware) {
            $object->setSyntaxTokenFactory($this->getTokenFactory());
        }
    }

    /**
     * Возвращает фабрику парсеров.
     * @return IParserFactory
     */
    protected function getParserFactory()
    {
        return $this->getFactory('parser');
    }

    /**
     * Возвращает фабрику токенов.
     * @return ITokenFactory
     */
    protected function getTokenFactory()
    {
        return $this->getFactory('token');
    }
}
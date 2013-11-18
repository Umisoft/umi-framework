<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox\factory;

use umi\templating\engine\ITemplateEngineFactory;
use umi\templating\exception\OutOfBoundsException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика шаблонизаторов.
 */
class TemplateEngineFactory implements ITemplateEngineFactory, IFactory
{
    use TFactory;

    /**
     * @var string $engines классы существующих шаблонизаторов и сериализаторов
     */
    public $engineClasses = [
        self::PHP_ENGINE  => 'umi\templating\engine\php\PhpTemplateEngine',
        self::TWIG_ENGINE => 'umi\templating\engine\twig\TwigTemplateEngine',
    ];

    /**
     * {@inheritdoc}
     */
    public function createTemplateEngine($type, array $options = [])
    {
        if (!isset($this->engineClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Cannot create template engine "{type}". Template engine "{type}" is not registered.',
                [
                    'type' => $type
                ]
            ));
        }

        return $this->getPrototype(
                $this->engineClasses[$type],
                ['umi\templating\engine\ITemplateEngine']
            )
            ->createInstance([$options]);
    }
}
 
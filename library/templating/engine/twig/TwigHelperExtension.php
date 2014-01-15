<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine\twig;

use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * Класс расширения для шаблонизатора Twig.
 * Регистрирует помощники для шаблонов как функции Twig.
 */
class TwigHelperExtension extends \Twig_Extension
{
    /**
     * @var string $name имя расширения
     */
    private $name;
    /**
     * @var IHelperCollection $templateHelpersCollection коллекция помощников для шаблонов
     */
    protected $templateHelpersCollection;

    /**
     * Конструктор.
     * @param string $name имя расширения
     * @param IHelperCollection $helpers
     */
    public function __construct($name, IHelperCollection $helpers)
    {
        $this->name = $name;
        $this->templateHelpersCollection = $helpers;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'umi\\' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $functions = [];

        foreach ($this->templateHelpersCollection->getList() as $helperName) {
            $helperCallable = $this->templateHelpersCollection->getCallable($helperName);

            $functions[] = new \Twig_SimpleFunction(
                $helperName,
                $helperCallable,
                ['is_safe' => ['html']]
            );
        }

        return $functions;
    }
}
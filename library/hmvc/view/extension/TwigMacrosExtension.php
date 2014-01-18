<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\extension;

use Twig_Extension;
use Twig_SimpleFunction;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\IDispatcher;

/**
 * Расширение для вызова макросов для Twig
 */
class TwigMacrosExtension extends Twig_Extension
{
    /**
     * Имя функции для вызова макроса
     */
    const MACROS_FUNCTION_NAME = 'macros';

    /**
     * @var IDispatcher $dispatcher диспетчер для вызова макроса
     */
    protected $dispatcher;
    /**
     * @var IComponent $component начальный компонент для поиска макроса
     */
    protected $component;

    /**
     * Конструктор.
     * @param IDispatcher $dispatcher диспетчер для вызова макроса
     * @param IComponent $component начальный компонент для поиска макроса
     */
    public function __construct(IDispatcher $dispatcher, IComponent $component) {
        $this->dispatcher = $dispatcher;
        $this->component = $component;
    }

    /**
    * {@inheritdoc}
    */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $callable = function() {
            $args = func_get_args();
            $macrosPath = array_shift($args);

            return $this->dispatcher->dispatchMacros($this->component, $macrosPath, $args)->getContent();
        };

        $macrosFunction = new Twig_SimpleFunction(
            self::MACROS_FUNCTION_NAME,
            $callable,
            ['is_safe' => ['html']]
        );

        return [$macrosFunction];
    }

}
 
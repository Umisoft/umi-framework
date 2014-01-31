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
use umi\hmvc\dispatcher\IDispatcher;
use umi\hmvc\view\helper\UrlHelper;

/**
 * Расширение Twig для подключения помощников вида.
 */
class ViewTwigExtension extends Twig_Extension
{
    /**
     * @var string $macrosFunctionName имя функции для вызова макроса
     */
    public $macrosFunctionName = 'macros';
    /**
     * @var string $urlFunctionName имя функции для генерации URL
     */
    public $urlFunctionName = 'url';

    /**
     * @var IDispatcher $dispatcher диспетчер для вызова макроса
     */
    protected $dispatcher;
    /**
     * @var UrlHelper $URLHelper
     */
    private $URLHelper;

    /**
     * Конструктор.
     * @param IDispatcher $dispatcher диспетчер для вызова макроса
     */
    public function __construct(IDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
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

        return [
            new Twig_SimpleFunction(
                $this->macrosFunctionName,
                $this->getMacrosHelper(),
                ['is_safe' => ['html']]
            ),

            new Twig_SimpleFunction(
                $this->urlFunctionName,
                $this->getURLHelper()
            )
        ];
    }

    /**
     * Возвращает помощник вида для генерации URL.
     * @return callable
     */
    protected function getURLHelper()
    {
        if (!$this->URLHelper) {
            $this->URLHelper = new UrlHelper($this->dispatcher);
        }
        return $this->URLHelper;
    }

    /**
     * Возвращает помощник вида для вызова макросов
     * @return callable
     */
    protected function getMacrosHelper()
    {
        return  function($macrosPath, array $args = []) {
            return $this->dispatcher->executeMacros($macrosPath, $args);
        };
    }

}
 
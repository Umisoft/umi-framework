<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\macros;

use umi\hmvc\dispatcher\IDispatchContext;
use umi\hmvc\exception\ViewRenderException;
use umi\hmvc\view\IView;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;

/**
 * Содержимое результата работы макроса, требующее шаблонизации.
 */
class MacrosView implements IView
{
    use TArrayAccess;
    use TPropertyAccess;

    /**
     * @var IDispatchContext $context контекст вызова макроса
     */
    protected $context;
    /**
     * @var string $templateName имя шаблона
     */
    protected $templateName;
    /**
     * @var array $variables переменные
     */
    protected $variables = [];

    /**
     * Конструктор.
     * @param IDispatchContext $context контекст вызова макроса
     * @param string $templateName имя шаблона
     * @param array $variables переменные шаблона
     */
    public function __construct(IDispatchContext $context, $templateName, array $variables = [])
    {
        $this->context = $context;
        $this->templateName = $templateName;
        $this->variables = $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            return $this->context->getComponent()->getViewRenderer()->render($this->templateName, $this->variables);
        } catch (\Exception $e) {

            $exception = new ViewRenderException(
                sprintf('Cannot render template "%s".', $this->templateName),
                0,
                $e
            );

            return $this->context->getDispatcher()->processMacrosError($this->context, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($attribute)
    {
        return $this->has($attribute) ? $this->variables[$attribute] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($attribute, $value)
    {
        $this->variables[$attribute] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute)
    {
        return isset($this->variables[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function del($attribute)
    {
        unset($this->variables[$attribute]);

        return $this;
    }
}
 
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\content;

use umi\hmvc\exception\RuntimeException;
use umi\hmvc\view\IView;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;
use umi\spl\error\ErrorHandler;

/**
 * Содержимое результата работы макроса или контроллера, требующее шаблонизации.
 */
class Content implements IContent
{
    use TArrayAccess;
    use TPropertyAccess;

    /**
     * @var IView $view
     */
    protected $view;
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
     * @param IView $view
     * @param string $templateName имя шаблона
     * @param array $variables переменные шаблона
     */
    public function __construct(IView $view, $templateName, array $variables = [])
    {
        $this->view = $view;
        $this->templateName = $templateName;
        $this->variables = $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            return $this->view->render($this->templateName, $this->variables);
        } catch (\Exception $e) {
            $exception = new RuntimeException(
                sprintf('Cannot render template "%s".', $this->templateName),
                0,
                $e
            );

            return ErrorHandler::toStringException($exception);
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
 
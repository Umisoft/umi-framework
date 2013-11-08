<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\result;

use umi\http\response\IResponse;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;

/**
 * Реализация обертки для результата работы контроллера.
 */
class ControllerResult implements IControllerResult, \ArrayAccess
{

    use TArrayAccess;

    /**
     * @var int $code код ответа
     */
    protected $code = IResponse::SUCCESS;
    /**
     * @var string $template шаблон
     */
    protected $template;
    /**
     * @var array $variables переменные
     */
    private $variables = [];

    /**
     * Конструктор.
     * @param string $template имя шаблона
     * @param array $variables переменные шаблона
     */
    public function __construct($template, array $variables = [])
    {
        $this->template = $template;
        $this->variables = $variables;
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

    /**
     * {@inheritdoc}
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->template;

    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }
}
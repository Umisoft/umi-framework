<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\response\model;

use umi\http\response\IResponse;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;

/**
 * Реализация обертки для результата работы контроллера.
 */
class DisplayModel implements IDisplayModel
{

    /**
     * @var int $code код ответа
     */
    protected $code = IResponse::SUCCESS;
    /**
     * @var string $templateName имя шаблона
     */
    protected $templateName;
    /**
     * @var array $variables переменные
     */
    private $variables = [];

    /**
     * Конструктор.
     * @param string $templateName имя шаблона
     * @param array $variables переменные шаблона
     * @param int $code HTTP код ответа
     */
    public function __construct($templateName, array $variables = [], $code = 200)
    {
        $this->templateName = $templateName;
        $this->variables = $variables;
        $this->code = $code;
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

    /**
     * {@inheritdoc}
     */
    public final function getTemplateName()
    {
        return $this->templateName;
    }
}

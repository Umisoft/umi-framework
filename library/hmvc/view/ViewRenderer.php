<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view;

use umi\hmvc\exception\InvalidArgumentException;
use umi\templating\engine\ITemplateEngine;
use umi\templating\engine\ITemplateEngineAware;
use umi\templating\engine\TTemplateEngineAware;

/**
 * Класс для рендеринга шаблона.
 */
class ViewRenderer implements IViewRenderer, ITemplateEngineAware
{
    use TTemplateEngineAware;

    /**
     * @var array $options опции
     */
    private $options;
    /**
     * @var ITemplateEngine $templateEngine шаблонизатор
     */
    private $templateEngine;

    /**
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function render($templateName, array $params = [])
    {
        return $this->getTemplateEngine()
            ->render($templateName, $params);
    }

    /**
     * Возвращает шаблонизатор созданный на основе опций.
     * @throws InvalidArgumentException при неверно заданных опциях
     * @return ITemplateEngine шаблонизатор
     */
    protected function getTemplateEngine()
    {
        if (!$this->templateEngine) {
            if (!isset($this->options[self::OPTION_TYPE])) {
                throw new InvalidArgumentException(
                    'Cannot setup template engine. Option "' . self::OPTION_TYPE . '" is required.'
                );
            }
            $this->templateEngine = $this->createTemplateEngine($this->options[self::OPTION_TYPE], $this->options);
        }

        return $this->templateEngine;
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine\php;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\templating\engine\BaseTemplateEngine;
use umi\templating\exception\RuntimeException;
use umi\templating\extension\helper\collection\IHelperCollection;

/**
 * PHP шаблонизатор.
 */
class PhpTemplateEngine extends BaseTemplateEngine implements ILocalizable
{
    use TLocalizable;

    /**
     * @var IHelperCollection $templateHelpersCollection
     */
    protected $templateHelpersCollection;
    /**
     * @var string $baseDirectory директория с шаблонами
     */
    protected $baseDirectory;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->baseDirectory = isset($options[self::OPTION_TEMPLATE_DIRECTORY]) ? $options[self::OPTION_TEMPLATE_DIRECTORY] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function render($templateFile, array $variables = [])
    {
        return (new PhpTemplate($this->baseDirectory, [$this, 'callHelper']))
            ->render($this->getTemplateFilename($templateFile), $variables);
    }

    /**
     * Magic method: вызывает помощник шаблонов.
     * @param string $name имя помощника шаблонов
     * @param array $arguments аргументы
     * @throws RuntimeException если коллекция помощников шаблонов не была внедрена
     * @return string
     */
    public function callHelper($name, array $arguments)
    {
        return call_user_func_array(
            $this->getExtensionAdapter()
                ->getCallable($name),
            $arguments
        );
    }
}
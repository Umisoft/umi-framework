<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine;

use umi\templating\extension\adapter\IExtensionAdapter;

/**
 * Интерфейс шаблонизатора.
 */
interface ITemplateEngine
{
    /** Директория расположения шаблонов */
    const OPTION_DIRECTORY = 'directory';
    /** Расширение файлов шаблонов */
    const OPTION_EXTENSION = 'extension';

    /**
     * Устанавливает расширение шаблонизатора.
     * @param IExtensionAdapter $adapter
     * @return self
     */
    public function setExtensionAdapter(IExtensionAdapter $adapter);

    /**
     * Отображает заданный шаблон используя переменные.
     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return string отображение
     */
    public function render($template, array $variables = []);
}
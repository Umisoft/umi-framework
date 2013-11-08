<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\script;

use umi\templating\extension\helper\type\head\BaseParamCollection;

/**
 * Список скриптов.
 */
class ScriptCollection extends BaseParamCollection
{
    /**
     * @var array $params список скриптов
     */
    protected static $params = [];

    /**
     * Добавляет файл скрипта в конец списка скриптов.
     * @param string $file имя файла
     * @param string $type тип скрипта
     * @return $this
     */
    public function appendFile($file, $type = 'text/javascript')
    {
        return $this->appendParam(['file', $file, $type]);
    }

    /**
     * Добавляет файл скрипта в начало списка скриптов.
     * @param string $file имя файла
     * @param string $type тип скрипта
     * @return $this
     */
    public function prependFile($file, $type = 'text/javascript')
    {
        return $this->prependParam(['file', $file, $type]);
    }

    /**
     * Добавляет скрипт в конец списка скриптов.
     * @param string $content скрипт
     * @param string $type тип скрипта
     * @return $this
     */
    public function appendScript($content, $type = 'text/javascript')
    {
        return $this->appendParam(['data', $content, $type]);
    }

    /**
     * Добавляет скрипт в начало списка скриптов.
     * @param string $content скрипт
     * @param string $type тип скрипта
     * @return $this
     */
    public function prependScript($content, $type = 'text/javascript')
    {
        return $this->prependParam(['data', $content, $type]);
    }

    /**
     * Возвращает HTML представление скриптов.
     * @return string
     */
    public function __toString()
    {
        $result = '';

        foreach (self::$params as $param) {
            list($type, $data, $scriptType) = $param;

            switch ($type) {
                case 'file':
                    $result .= '<script type="' . $scriptType . '" src="' . $data . '"></script>';
                    break;
                case 'data':
                    $result .= '<script type="' . $scriptType . '">' . $data . '</script>';
                    break;
            }
        }

        return $result;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\style;

use umi\templating\extension\helper\type\head\BaseParamCollection;

/**
 * Список стилей.
 */
class StyleCollection extends BaseParamCollection
{
    /**
     * @var array $params список стилей
     */
    protected static $params = [];

    /**
     * Добавляет файл стили в конец списка стилей.
     * @param string $file имя файла стилей
     * @param string $type тип стилей
     * @return $this
     */
    public function appendFile($file, $type = 'text/css')
    {
        return $this->appendParam(['file', $file, $type]);
    }

    /**
     * Добавляет файл стилей в начало списка стилей.
     * @param string $file имя файла стилей
     * @param string $type тип стилей
     * @return $this
     */
    public function prependFile($file, $type = 'text/css')
    {
        return $this->prependParam(['file', $file, $type]);
    }

    /**
     * Добавляет стили в конец списка стилей.
     * @param string $content содержание стилей
     * @param string $type тип стилей
     * @return $this
     */
    public function appendStyle($content, $type = 'text/css')
    {
        return $this->appendParam(['data', $content, $type]);
    }

    /**
     * Добавляет стили в начало списка стилей.
     * @param string $content содержание стилей
     * @param string $type тип стилей
     * @return $this
     */
    public function prependStyle($content, $type = 'text/css')
    {
        return $this->prependParam(['data', $content, $type]);
    }

    /**
     * Возвращает HTML представление стилей.
     * @return string
     */
    public function __toString()
    {
        $result = '';

        foreach (self::$params as $param) {
            list($type, $data, $styleType) = $param;

            switch ($type) {
                case 'file':
                    $result .= '<link rel="stylesheet" type="' . $styleType . '" href="' . $data . '" />';
                    break;
                case 'data':
                    $result .= '<style type="' . $styleType . '">' . $data . '</style>';
                    break;
            }
        }

        return $result;
    }
}
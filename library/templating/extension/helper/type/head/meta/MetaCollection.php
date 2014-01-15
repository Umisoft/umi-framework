<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\meta;

use umi\templating\extension\helper\type\head\BaseParamCollection;

/**
 * Список мета информации.
 */
class MetaCollection extends BaseParamCollection
{
    /**
     * @var array $params список мета информации
     */
    protected static $params = [];

    /**
     * @var string $charset кодировка
     */
    protected static $charset;

    /**
     * Добавляет мета http-equiv параметр в конец списка параметров.
     * @param string $httpEquiv имя параметра
     * @param string $value значение параметра
     * @return $this
     */
    public function appendHttpEquiv($httpEquiv, $value)
    {
        return $this->appendParam(['http-equiv', $httpEquiv, $value]);
    }

    /**
     * Добавляет мета http-equiv параметр в начало списка параметров.
     * @param string $httpEquiv имя параметра
     * @param string $value значение параметра
     * @return $this
     */
    public function prependHttpEquiv($httpEquiv, $value)
    {
        return $this->prependParam(['http-equiv', $httpEquiv, $value]);
    }

    /**
     * Добавляет мета параметр в конец списка параметров.
     * @param string $name имя параметра
     * @param string $value значение параметра
     * @return $this
     */
    public function appendName($name, $value)
    {
        return $this->appendParam(['name', $name, $value]);
    }

    /**
     * Добавляет мета параметр в начало списка параметров.
     * @param string $name имя параметра
     * @param string $value значение параметра
     * @return $this
     */
    public function prependName($name, $value)
    {
        return $this->prependParam(['name', $name, $value]);
    }

    /**
     * Устанавливает кодировку в мета информацию.
     * @param string $charset кодировка
     * @return $this
     */
    public function setCharset($charset)
    {
        self::$charset = $charset;

        return $this;
    }

    /**
     * Возвращает HTML представление мета тэгов.
     * @return string
     */
    public function __toString()
    {
        $result = '';

        if (self::$charset) {
            $result .= '<meta charset="' . self::$charset . '" />'; //todo: HTML5 only?
        }

        foreach (self::$params as $param) {
            list($type, $name, $value) = $param;

            switch ($type) {
                case 'http-equiv':
                    $result .= '<meta http-equiv="' . $name . '" content="' . $value . '" />';
                    break;
                case 'name':
                    $result .= '<meta name="' . $name . '" content="' . $value . '" />';
                    break;
            }
        }

        return $result;
    }
}
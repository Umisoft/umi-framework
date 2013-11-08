<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\route\exception\InvalidArgumentException;
use umi\route\exception\OutOfBoundsException;
use umi\route\exception\RuntimeException;

/**
 * Правило маршрутизатора на основе simple выражений.
 * Примеры:
 *    {module}/{controller}/{action}
 *    {module:string}/{controller:integer}/{action:string}
 */
class SimpleRoute extends RegexpRoute implements IRoute, ILocalizable
{
    /**
     * @var array $types типы параметров маршрутизатора.
     * [
     *  integer => целое число
     *  float => число с плавающей точкой
     *  string => строка(до управляющего символа - /)
     *  text => строка(до конца URL, игнорирует управляющий символ)
     * ]
     */
    public $types = [
        'integer' => '\d+',
        'guid'    => '\S{8}-\S{4}-\S{4}-\S{4}-\S{12}',
        'float'   => '[0-9]+[.]?[0-9]*',
        'string'  => '[^/]+',
        'text'    => '.+',
    ];

    /**
     * {@inheritdoc}
     */
    public function match($url)
    {
        $route = $this->getRegExpRoute($this->route);

        return $this->matchRegExp("#^{$route}#", $url, $this->params);
    }

    /**
     * {@inheritdoc}
     */
    public function assemble(array $params = [], array $options = ['forceDefaults' => false])
    {
        return preg_replace_callback(
            '#(/?)\{(\S+?)(:(\S+?))?\}#',
            function (array $matches) use ($params, $options) {
                $name = $matches[2];
                $type = isset($matches[4]) ? $matches[4] : 'string';

                if (array_key_exists($name, $params)) {
                    $startMod = $matches[1];
                    $param = $params[$name];

                    if (!$this->checkParam($param, $type)) {
                        throw new InvalidArgumentException($this->translate(
                            'Param "{name}" does not match type "{type}".',
                            ['name' => $name, 'type' => $type]
                        ));
                    }

                    if ($this->getOption($name, $this->defaults) == $param &&
                        !$this->getOption(self::OPTION_FORCE_DEFAULT, $options)
                    ) {
                        return '';
                    }

                    return $startMod . $param;
                } elseif (!$this->isRequiredParam($name)) {
                    return '';
                } else {
                    throw new RuntimeException($this->translate(
                        'Param "{name}" is required.',
                        ['name' => $name]
                    ));
                }
            },
            $this->route
        );
    }

    /**
     * Проверяет параметр на сооветствие указанному типу.
     * @param string $param параметр
     * @param string $type тип
     * @return bool
     */
    protected function checkParam($param, $type)
    {
        $type = $this->getTypeRegexp($type);

        return (bool) preg_match("#^{$type}$#", $param);
    }

    /**
     * Проверяет, является ли заданый параметр обязательным.
     * @param string $name имя параметра
     * @return bool
     */
    protected function isRequiredParam($name)
    {
        return !array_key_exists($name, $this->defaults);
    }

    /**
     * Возвращает регулярное выражения для заданного типа.
     * @param string $type тип
     * @throws OutOfBoundsException если тип не найден
     * @return string регуляроне выражение
     */
    protected function getTypeRegexp($type)
    {
        if (!isset($this->types[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Unknown selected type "{type}" param.',
                ['type' => $type]
            ));
        }

        return $this->types[$type];
    }

    /**
     * Заменяет simple выражения на именованные регулярные выражения
     * @used-by $this::match
     * @param string $route правило маршрута
     * @return string
     */
    protected function getRegExpRoute($route)
    {
        return preg_replace_callback(
            '#(/?)\{(\S+?)(:(\S+?))?\}#',
            function (array $matches) {
                $startMod = $matches[1];
                $name = $matches[2];
                $type = isset($matches[4]) ? $matches[4] : 'string';

                $type = $this->getTypeRegexp($type);

                $regexp = $startMod . "(?P<$name>$type)";
                if (!$this->isRequiredParam($name)) {
                    $regexp = "({$regexp})?";
                }

                return $regexp;
            },
            $route
        );
    }

    /**
     * Возвращает значение массива по ключу, либо NULL.
     * @param string $option ключ массива
     * @param array $options массив
     * @return string
     */
    private function getOption($option, array $options)
    {
        return array_key_exists($option, $options) ? $options[$option] : null;
    }
}
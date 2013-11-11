<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\RuntimeException;

/**
 * Правило маршрутизатора на основе simple выражений.
 * Примеры:
 *    {module}/{controller}/{action}
 *    $rules => [
 *        'module' => '.+'
 *    ]
 */
class ExtendedRoute extends SimpleRoute
{
    const OPTION_RULES = 'rules';
    const OPTION_DEFAULT_RULE = 'defaultRule';

    /**
     * @var array $rules правила переменных маршрутизатора
     */
    protected $rules = [];
    /**
     * @var string $defaultRule правило по-умолчанию
     */
    protected $defaultRule = '[^/]+';

    public function __construct(array $options = [], array $subroutes = [])
    {
        $this->rules = isset($options[self::OPTION_RULES]) ? $options[self::OPTION_RULES] : [];
        $this->defaultRule = isset($options[self::OPTION_DEFAULT_RULE]) ? $options[self::OPTION_DEFAULT_RULE] : $this->defaultRule;
    }

    /**
     * {@inheritdoc}
     */
    public function assemble(array $params = [])
    {
        return preg_replace_callback(
            '#(/?)\{(\S+?)\}#',
            function (array $matches) use ($params) {
                $name = $matches[2];
                $rule = $this->getRule($name);

                if (array_key_exists($name, $params)) {
                    $startMod = $matches[1];
                    $param = $params[$name];

                    if (!$this->checkParam($param, $rule)) {
                        throw new InvalidArgumentException($this->translate(
                            'Param "{name}" does not match regular expression "{rule}".',
                            ['name' => $name, 'rule' => $rule]
                        ));
                    }

                    if ($this->getOption($name, $this->defaults) == $param) {
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
     * {@inheritdoc}
     */
    public function getRegExpRoute($route)
    {
        return preg_replace_callback(
            '#(/?)\{(\S+?)\}#',
            function (array $matches) {
                $startMod = $matches[1];
                $name = $matches[2];

                $type = $this->getRule($name);

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
     * Возвращает регулярное выражение для переменной правила
     * @param string $name имя переменной в правиле
     * @return string
     */
    protected function getRule($name)
    {
        return isset($this->rules[$name]) ? $this->rules[$name] : $this->defaultRule;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkParam($param, $rule)
    {
        return (bool) preg_match("#^{$rule}$#", $param);
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
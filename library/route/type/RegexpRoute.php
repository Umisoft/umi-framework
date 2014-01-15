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
use umi\route\exception\RuntimeException;

/**
 * Правило маршрутизатора на основе регулярных выражений.
 * Важно: части регулярного выражения, необходимые в запросе,
 * требуется именовать(к примеру (?P<id>\d+). Валидация неименованых частей работает,
 * но данные из них не будут возвращены.
 * @example users/(?P<action>(\S+))
 */
class RegexpRoute extends BaseRoute implements ILocalizable
{
    use TLocalizable;

    /**
     * @var array $assembleParams параметры
     */
    private $assembleParams;

    /**
     * {@inheritdoc}
     */
    public function match($url)
    {
        return $this->matchRegExp("#^{$this->route}#", $url, $this->params);
    }

    /**
     * {@inheritdoc]
     */
    public function assemble(array $params = [])
    {
        //TODO: change that!!!!
        $this->assembleParams = $params + $this->defaults;

        return preg_replace_callback('#\(\?P?<(.+?)>(.+?)\)#', [$this, 'assembleReplaceCallback'], $this->route);
    }

    /**
     * Выполняет проверку строки по регулярному выражению.
     * @param string $regexp регулярное выражение
     * @param string $string строка
     * @param array $params параеметры
     * @return bool|int кол-во подошедших символов, либо false - если не подходит
     */
    protected function matchRegExp($regexp, $string, array &$params)
    {
        if (preg_match($regexp, $string, $params)) {
            $matched = strlen($params[0]);
            $this->filterMatchedParams($params);

            return $matched;
        } else {
            return false;
        }
    }

    /**
     * Возвращает значение для именованной части регулярного выражения.
     * @used-by $this::assemble regexp callback
     * @param array $matches найденное совпадение
     * @throws RuntimeException если параметр не найден
     * @return string значение
     */
    private function assembleReplaceCallback(array $matches)
    {
        if (!isset($this->assembleParams[$matches[1]])) {
            throw new RuntimeException($this->translate(
                'Assemble param "{name}" does not exist.',
                ['name' => $matches[1]]
            ));
        }

        return $this->assembleParams[$matches[1]];
    }

    /**
     * Удаляет numeric matches из массива.
     * @param array $params массив параметров
     */
    private function filterMatchedParams(&$params)
    {
        $i = 0;
        while (true) {
            if (!isset($params[$i])) {
                break;
            }
            unset($params[$i]);
            $i++;
        }

        foreach ($params as $k => $param) {
            if ($param === '') {
                unset($params[$k]);
            }
        }
    }
}
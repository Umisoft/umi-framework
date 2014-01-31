<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head\title;

/**
 * Заголовок страницы.
 */
class TitleCollection
{
    /**
     * @var string $title заголовок страницы
     */
    protected static $title;

    /**
     * Устанавливает заголовок страницы.
     * @param string $title заголовок
     * @return $this
     */
    public function setTitle($title)
    {
        self::$title = $title;

        return $this;
    }

    /**
     * Возвращает HTML представление мета тэгов.
     * @return string
     */
    public function __toString()
    {
        $result = '';

        if (self::$title) {
            $result = '<title>' . self::$title . '</title>';
        }

        return $result;
    }
}
 
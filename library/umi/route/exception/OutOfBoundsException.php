<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\exception;

/**
 * Создается исключение, если значение не является действительным ключем.
 * Это соответствует ошибкам, которые не могут быть обнаружены во время компиляции.
 */
class OutOfBoundsException extends \OutOfBoundsException implements IException
{
}
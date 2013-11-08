<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\exception;

/**
 * Исключения, связанные с ошибками Cross-site request forgery.
 * Исключение бросается при проверки CSRF элемента формы.
 */
class InvalidCsrfTokenException extends \InvalidArgumentException implements IException
{
}
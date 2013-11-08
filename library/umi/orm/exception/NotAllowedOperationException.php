<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\exception;

/**
 * Исключения, связанные с попыткой выполнить операцию на сущностью ORM, которая запрещена по каким-либо причинам.
 * Например, при попытки удалить базовый тип, либо при попытке удалить поле, которое есть у родительского типа.
 */
class NotAllowedOperationException extends RuntimeException
{
}
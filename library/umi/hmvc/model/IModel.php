<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\model;

/**
 * Интерфейс MVC-модели.
 * Модели, имплементирующие данный интерфейс автоматически внедряются в конструктор контроллера.
 * Фактически, модель не обязательно должна имплементировать этот интерфейс.
 * В случае, когда вы хотите создать модель не имплементирующую данный интерфейс, вам необходимо будет вызвать метод getModel() у контроллера.
 */
interface IModel
{
}
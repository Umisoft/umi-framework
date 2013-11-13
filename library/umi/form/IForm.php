<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

use umi\event\IEventObservant;
use umi\form\binding\IDataBinding;
use umi\form\fieldset\IFieldset;

/**
 * Интерфейс формы.
 * Форма является именнованой группой полей.
 */
interface IForm extends IFieldset, IEventObservant
{
    /**
     * Возвращает action формы.
     * @return string
     */
    public function getAction();

    /**
     * Возвращает метод отправки формы.
     * @return string
     */
    public function getMethod();

    /**
     * Выставляет является ли данная форма - подформой.
     * Вызов данного метода вызывает перестраивание имен элементов формы.
     * @param bool $isSubform является ли форма подформой
     * @return self
     */
    public function setIsSubForm($isSubform);

    /**
     * Устанавливает связанный объект к форме.
     * @param IDataBinding $object объект
     * @return self
     */
    public function bindObject(IDataBinding $object);

    /**
     * Возвращает установленные данные или установленный в форму объект.
     * @return array|object
     */
    public function getData();
}
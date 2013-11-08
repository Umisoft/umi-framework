<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\fieldset;

use umi\form\element\IElement;
use umi\form\IFormEntity;

/**
 * Интерфейс группы полей.
 * Группа полей, в понимании данного компонента, набор разнотипных элементов: IElement или IFieldset.
 * Другими словами группа может хранить в себе элементы, группы полей либо под-формы.
 */
interface IFieldset extends IFormEntity, \Traversable
{
    /**
     * Возвращает заданный элемент по имени.
     * @param string $name имя элемента
     * @return IFormEntity|IElement|IFieldset элемент
     */
    public function getElement($name);

    /**
     * Устанавливает данные в форму.
     * @param array $data данные
     * @return self
     */
    public function setData(array $data);

    /**
     * Возвращает данные установленные в форму.
     * @return array
     */
    public function getData();
}
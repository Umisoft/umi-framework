<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

/**
 * Интерфейс HTML элемента.
 */
interface IFormEntity
{
    /**
     * Опция элемента, отключающая его в выводе IFieldset::getData().
     */
    const OPTION_EXCLUDE = 'exclude';

    /**
     * Возвращает имя элемента.
     * @return string
     */
    public function getName();

    /**
     * Возвращает название элемента формы.
     * @return string
     */
    public function getLabel();

    /**
     * Устанавливает название элемента формы.
     * @param string $label
     * @return string
     */
    public function setLabel($label);

    /**
     * Возвращает все аттрибуты в виде ассоциативного массива.
     * @return \ArrayObject
     */
    public function getAttributes();

    /**
     * Возвращает массив опций.
     * @return \ArrayObject
     */
    public function getOptions();

    /**
     * Возвращает сообщения валидации.
     * @return array
     */
    public function getMessages();

    /**
     * Возвращает значение валидности элемента.
     * @return bool
     */
    public function isValid();
}
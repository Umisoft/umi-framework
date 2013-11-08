<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form;

use umi\form\exception\OutOfBoundsException;

/**
 * Интерфейс фабрики элементов формы.
 */
interface IEntityFactory
{

    /**
     * Создает элементы формы на основе конфигурации.
     * @param array $config конфигурация
     * @return IFormEntity[] элементы формы
     */
    public function createEntities(array $config);

    /**
     * Создает элемент формы. Это может быть как просто элемент,
     * так и коллекция элементов.
     * @param string $name имя элемента
     * @param array $config конфигурация элемента, включая аттрибуты и опции
     * @throws OutOfBoundsException если тип элемента не поддерживается
     * @return IFormEntity
     */
    public function createEntity($name, array $config);

    /**
     * Создает форму на основе конфигурации.
     * @param array $config конфигурация
     * @return IForm
     */
    public function createForm(array $config);
}
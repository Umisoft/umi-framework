<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\orm\exception\NonexistentEntityException;
use umi\orm\metadata\field\IField;

/**
 * Тип данных.
 * Представляет собой досуп к мета-информации об объекте (его группы и свойства).
 * Все объекты создаются на основе объектного типа.
 */
interface IObjectType
{

    /**
     * Разделитель для формирования полного пути к типу
     */
    const PATH_SEPARATOR = '.';

    /**
     * Имя базового типа объекта
     */
    const BASE = 'base';
    /**
     * Базовый тип объекта
     */
    const NAME_SEPARATOR = '.';

    /**
     * Возвращает уникальное имя типа
     * @return string
     */
    public function getName();

    /**
     * Возвращает имя класса для создания экземпляров объектов данного типа
     * @return string|null имя класса, либо null если используется класс по умолчанию (umi\orm\object\Object)
     */
    public function getObjectClass();

    /**
     * Возвращает список экземпляров всех полей
     * @return IField[] массив вида [fieldName => IField, ...]
     */
    public function getFields();

    /**
     * Возвращает список всех локализованных полей типа
     * @return IField[] массив вида [fieldName => IField, ...]
     */
    public function getLocalizedFields();

    /**
     * Проверяет, существует ли поле с указанным именем в типе
     * @param string $fieldName имя поля
     * @return bool
     */
    public function getFieldExists($fieldName);

    /**
     * Возвращает поле по его имени
     * @param string $fieldName имя поля
     * @throws NonexistentEntityException если поле с указанным именем не существует
     * @return IField
     */
    public function getField($fieldName);

}

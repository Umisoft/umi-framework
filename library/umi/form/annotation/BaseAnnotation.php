<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\annotation;

/**
 * Базовый класс аннотаций конфига.
 */
abstract class BaseAnnotation implements IAnnotation
{
    /**
     * @var string $value значение
     */
    protected $value;

    /**
     * Конструктор.
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine;

use umi\templating\exception\OutOfBoundsException;

/**
 * Интерфейс фабрики шаблонизаторов.
 */
interface ITemplateEngineFactory
{
    /** PHP шаблонизатор */
    const PHP_ENGINE = 'php';
    /** Twig шаблонизатор */
    const TWIG_ENGINE = 'twig';

    /**
     * Создает шаблонизатор заданного типа.
     * @param string $type тип шаблонизатора
     * @param array $options опции шаблонизатора
     * @throws OutOfBoundsException если заданный тип шаблонизатора не зарегистрирован
     * @return ITemplateEngine
     */
    public function createTemplateEngine($type, array $options = []);

    /**
     * Устанавливает инициализатор для указанного типа шаблонизатора.
     * @param string $type тип шаблонизатора
     * @param callable $initializer инициализатор, в который передается экземпляр шаблонизатора после его создания
     * @throws OutOfBoundsException если заданный тип шаблонизатора не зарегистрирован
     * @return $this
     */
    public function setInitializer($type, callable $initializer);
}
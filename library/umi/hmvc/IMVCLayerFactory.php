<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc;

use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\IView;

/**
 * Интерфейс для фабрики MVC слоев.
 */
interface IMVCLayerFactory
{
    /**
     * Создает фабрику контроллеров.
     * @param array $options опции фабрики
     * @return IControllerFactory
     */
    public function createControllerFactory(array $options);

    /**
     * Создает фабрику моделей.
     * @param array $options опции фабрики
     * @return IModelFactory
     */
    public function createModelFactory(array $options);

    /**
     * Создает слой отображения.
     * @param array $options опции
     * @return IView
     */
    public function createView(array $options);
}
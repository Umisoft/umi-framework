<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'name'     => HMVCTools::NAME,
    'class'    => __NAMESPACE__ . '\HMVCTools',
    'servicingInterfaces' => [
        'umi\hmvc\IMVCLayerAware',
        'umi\hmvc\component\IComponentAware',
        'umi\hmvc\controller\result\IControllerResultAware',
        'umi\hmvc\component\response\IComponentResponseAware',
        'umi\hmvc\component\request\IComponentRequestAware',
        'umi\hmvc\view\template\IViewExtensionFactoryAware',
    ],
    'services' => [
        'umi\hmvc\component\IComponentFactory',
        'umi\hmvc\component\request\IComponentRequestFactory'
    ]
];
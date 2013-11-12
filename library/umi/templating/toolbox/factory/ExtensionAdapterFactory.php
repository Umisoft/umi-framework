<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox\factory;

use umi\templating\extension\adapter\IExtensionAdapterFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика адаптеров для подключения расширений шаблонизаторов.
 */
class ExtensionAdapterFactory implements IExtensionAdapterFactory, IFactory
{
    use TFactory;

    /**
     * @var string $extensionAdapterClass класс адаптера для расширения шаблонизаторов
     */
    public $extensionAdapterClass = 'umi\templating\extension\adapter\ExtensionAdapter';

    /**
     * {@inheritdoc}
     */
    public function createExtensionAdapter()
    {
        return $this->createInstance(
            $this->extensionAdapterClass,
            [],
            ['umi\templating\extension\adapter\ExtensionAdapter']
        );
    }
}
 
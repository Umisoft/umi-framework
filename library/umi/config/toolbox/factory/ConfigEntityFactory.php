<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\toolbox\factory;

use umi\config\entity\factory\IConfigEntityFactory;
use umi\config\exception\OutOfBoundsException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика сущностей конфигурации.
 */
class ConfigEntityFactory implements IConfigEntityFactory, IFactory
{

    use TFactory;

    /**
     * @var string $valueClass класс для значения конфигурации
     */
    public $valueClass = 'umi\config\entity\value\ConfigValue';
    /**
     * @var string $configSourceClass класс "источника" конфигурации
     */
    public $configSourceClass = 'umi\config\entity\ConfigSource';
    /**
     * @var array $separateConfigClasses классы отдельных конфигураций
     */
    public $separateConfigClasses = [
        self::SEPARATE_LAZY => 'umi\config\entity\LazyConfigSource',
    ];

    /**
     * {@inheritdoc}
     */
    public function createValue()
    {
        return $this->createInstance(
            $this->valueClass,
            [],
            ['umi\config\entity\value\IConfigValue']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createConfigSource($alias, array &$source)
    {
        return $this->createInstance(
            $this->configSourceClass,
            [$source, $alias],
            ['umi\config\entity\IConfigSource']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSeparateConfigSource($type, $alias)
    {
        if (!isset($this->separateConfigClasses[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Separate config type "{type}" is not found',
                [$type]
            ));
        }

        return $this->createInstance(
            $this->separateConfigClasses[$type],
            [$alias],
            ['umi\config\entity\ISeparateConfigSource']
        );
    }
}
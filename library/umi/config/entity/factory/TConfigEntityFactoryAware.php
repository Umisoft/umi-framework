<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity\factory;

use umi\config\entity\IConfigSource;
use umi\config\entity\ISeparateConfigSource;
use umi\config\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки создания сущностей конфигурации.
 */
trait TConfigEntityFactoryAware
{
    /**
     * @var IConfigEntityFactory $_configEntityFactory фабрика сущностей конфигурации
     */
    private $_configEntityFactory;

    /**
     * Устанавливает фабрику сущностей.
     * @param IConfigEntityFactory $configFactory фабрика сущностей
     */
    public final function setConfigEntityFactory(IConfigEntityFactory $configFactory)
    {
        $this->_configEntityFactory = $configFactory;
    }

    /**
     * Создает конфигурацию, на основе источника данных.
     * @param string $alias символическое имя конфигурации
     * @param array $source конфигурация
     * @return IConfigSource
     */
    protected final function createConfigSource($alias, array $source)
    {
        return $this->getConfigEntityFactory()
            ->createConfigSource($alias, $source);
    }

    /**
     * Создает отдельную конфигурацию.
     * @param string $type тип отдельной конфигурации
     * @param string $alias символическое имя конфигурации
     * @return ISeparateConfigSource
     */
    protected final function createSeparateConfigSource($type, $alias)
    {
        return $this->getConfigEntityFactory()
            ->createSeparateConfigSource($type, $alias);
    }

    /**
     * Возвращает фабрику сущностей конфигурации.
     * @return IConfigEntityFactory
     * @throws RequiredDependencyException
     */
    private function getConfigEntityFactory()
    {
        if (!$this->_configEntityFactory instanceof IConfigEntityFactory) {
            throw new RequiredDependencyException(sprintf(
                'Config entity factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_configEntityFactory;
    }
}
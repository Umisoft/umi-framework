<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io;

use umi\config\entity\IConfigSource;
use umi\config\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки работы с I/O сервисом конфигурации.
 */
trait TConfigIOAware
{
    /**
     * @var IConfigIO $_configIO I/O config service
     */
    private $_configIO;

    /**
     * Устанавливает I/O сервис.
     * @param IConfigIO $configIO I/O сервис
     */
    public final function setConfigIO(IConfigIO $configIO)
    {
        $this->_configIO = $configIO;
    }

    /**
     * Читает конфигурацию с заданным символическим именем.
     * @param string $alias символическое имя
     * @return IConfigSource
     */
    protected final function readConfig($alias)
    {
        return $this->getConfigIO()
            ->read($alias);
    }

    /**
     * Записывает конфигурацию.
     * @param IConfigSource $config
     * @return $this
     */
    protected final function writeConfig(IConfigSource $config)
    {
        $this->getConfigIO()
            ->write($config);

        return $this;
    }

    /**
     * Возврващает I/O сервис конфигурации.
     * @return IConfigIO
     * @throws RequiredDependencyException если сервис не был внедрен
     */
    private final function getConfigIO()
    {
        if (!$this->_configIO) {
            throw new RequiredDependencyException(sprintf(
                'Config IO service is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_configIO;
    }

}
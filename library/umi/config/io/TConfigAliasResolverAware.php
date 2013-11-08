<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io;

use umi\config\exception\RequiredDependencyException;
use umi\config\exception\RuntimeException;

/**
 * Трейт для внедрения поддержки работы с символическими именами конфигурации.
 * @internal
 */
trait TConfigAliasResolverAware
{
    /**
     * @var IConfigIO $_configIO I/O config service
     */
    private $_configIO;

    /**
     * Устанавливает I/O сервис.
     * @param IConfigIO $configIO I/O сервис
     */
    public function setConfigIO(IConfigIO $configIO)
    {
        $this->_configIO = $configIO;
    }

    /**
     * Возвращает имена локального и мастер файла конфигурации
     * для заданного имени.
     * @param string $alias имя конфигурации
     * @throws RuntimeException если имя конфигурации
     * @throws RequiredDependencyException если не внедрены инструменты работы с конфигурацией
     * @return array
     */
    protected final function getFilesByAlias($alias)
    {
        return $this->getConfigIO()
            ->getFilesByAlias($alias);
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
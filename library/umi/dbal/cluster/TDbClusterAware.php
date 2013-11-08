<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster;

use umi\dbal\exception\RequiredDependencyException;

/**
 * Трейт для поддержки работы с бд.
 */
trait TDbClusterAware
{

    /**
     * @var IDbCluster $_dbCluster компонент для работы с бд
     */
    private $_dbCluster;

    /**
     * Устанавливает компонент для работы с бд.
     * @param IDbCluster $dbCluster
     * @return self
     */
    public function setDbCluster(IDbCluster $dbCluster)
    {
        $this->_dbCluster = $dbCluster;
    }

    /**
     * Возвращает компонент для работы с бд.
     * @throws RequiredDependencyException
     * @return IDbCluster
     */
    protected function getDbCluster()
    {
        if (!$this->_dbCluster) {
            throw new RequiredDependencyException(sprintf(
                'DB cluster is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_dbCluster;
    }

}
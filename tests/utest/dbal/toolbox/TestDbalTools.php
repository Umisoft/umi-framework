<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal\toolbox;

use umi\dbal\cluster\IDbCluster;
use umi\dbal\toolbox\DbalTools;

/**
 * Инструменты для работы с тестовой БД
 */
class TestDbalTools extends DbalTools
{
    private static $testCluster;

    /**
     * Возвращает кластер БД
     * @return IDbCluster
     */
    protected function getCluster() {
        if (!self::$testCluster) {
            self::$testCluster = parent::getCluster();
        }
        return self::$testCluster;
    }
}

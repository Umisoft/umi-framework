<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver;

use umi\dbal\driver\IDbDriver;
use umi\dbal\driver\mysql\MySqlDriver;
use umi\dbal\toolbox\factory\DbDriverFactory;
use utest\TestCase;

/**
 * Тестирование работы со схемой таблицы
 *
 */
class DbDriverFactoryTest extends TestCase
{
    public function testTable()
    {

        $dbDriverFactory = new DbDriverFactory();
        $this->resolveOptionalDependencies($dbDriverFactory);

        $this->assertInstanceOf(
            'umi\dbal\driver\IDbDriver',
            $dbDriverFactory->create('sqlite'),
            'Ожидается, что IDbDriverFactory::create() вернет IDbDriver'
        );

        $e = null;
        try {
            $dbDriverFactory->create('wrong_driver_type');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить несуществующий драйвер'
        );
    }
}

/**
 * Class TestDriver тестовый драйвер
 */
class TestDriver extends MySqlDriver implements IDbDriver
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return false;
    }
}

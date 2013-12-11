<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm;

use Doctrine\DBAL\Connection;
use umi\dbal\cluster\IDbCluster;
use utest\dbal\SqlLogger;
use utest\dbal\TDbalSupport;
use utest\event\TEventSupport;
use utest\i18n\TI18nSupport;
use utest\TestCase;
use utest\validation\TValidationSupport;

/**
 * Тест кейс для ORM c использованием подключения к БД
 */
abstract class ORMDbTestCase extends TestCase
{

    use TORMSupport;
    use TEventSupport;
    use TValidationSupport;
    use TI18nSupport;
    use TORMSetup;

    const SYSTEM_HIERARCHY = 'system_hierarchy';
    const SYSTEM_MENU = 'system_menu';
    const USERS_USER = 'users_user';
    const USERS_GROUP = 'users_group';
    const USERS_PROFILE = 'users_profile';
    const BLOGS_BLOG = 'blogs_blog';
    const BLOGS_POST = 'blogs_post';
    const BLOGS_SUBSCRIBER = 'blogs_blog_subscribers';
    const GUIDES_CITY = 'guides_city';
    const GUIDES_COUNTRY = 'guides_country';

    const METADATA_DIR = __DIR__;

    /**
     * @var null идентификатор сервера БД, который будет использован для всего тест кейса
     * Если null, будет выбран сервер по умолчанию.
     */
    protected $usedDbServerId = 'sqliteMaster';

    /**
     * @var Connection $connection
     */
    protected $connection = null;

    public function setUp()
    {
        $this->registerEventTools();
        $this->registerValidationTools();
        $this->registerI18nTools();
        $this->registerDbalTools();
        $this->registerORMTools();

        $this->setUpORM($this->usedDbServerId);

        /** @var IDbCluster $cluster */
        $cluster = $this
            ->getTestToolkit()
            ->getService('umi\dbal\cluster\IDbCluster');
        $this->connection = $cluster->getServer($this->usedDbServerId)->getConnection();
        $this->connection->getConfiguration()->setSQLLogger(new SqlLogger());
        parent::setUp();

        $this->resetQueries();
    }

    protected function tearDown()
    {
        $this->tearDownORM();
        parent::tearDown();
    }

    /**
     * Логированные запросы, выполненные через $this->usedConnection
     * @param bool $withValues Подставлять ли реальные значения в логированные запросы
     * @return array
     */
    protected function getQueries($withValues = false)
    {
        return $this->sqlLogger()->getQueries($withValues);
    }

    /**
     * Logged queries as type-params pairs
     * @param bool $withParams Whether to append logged params in each result
     *
     * @return array [ ['select', [':foo'=>'foo', ':bar'=>121 ...]] ]
     */
    protected function getQueryTypesWithParams($withParams = true)
    {
        return $this->sqlLogger()->getQueryTypesWithParams($withParams);
    }

    /**
     * Логированные запросы, выполненные через $this->usedConnection с ограничением по типу
     * @param string $type select|update|insert|delete
     *
     * @return array
     */
    protected function getOnlyQueries($type)
    {
        return $this->sqlLogger()->getOnlyQueries($type);
    }

    /**
     * Сбросить лог запросов
     */
    protected function resetQueries()
    {
        $this->sqlLogger()->resetQueries();
    }

    /**
     * @return SqlLogger
     */
    protected function sqlLogger()
    {
        return $this->connection
            ->getConfiguration()
            ->getSQLLogger();
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm;

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
    protected $usedDbServerId = null;

    public function setUp()
    {
        $this->registerEventTools();
        $this->registerValidationTools();
        $this->registerI18nTools();
        $this->registerDbalTools();
        $this->registerORMTools();

        $this->setUpORM($this->usedDbServerId);
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->tearDownORM();
        parent::tearDown();
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\cache;

use utest\event\TEventSupport;
use utest\TestCase;

/**
 * Тест кейс для кеширования
 */
abstract class CacheTestCase extends TestCase
{
    use TCacheSupport;
    use TEventSupport;
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerEventTools();
        $this->registerCacheTools();

        parent::setUp();
    }
}
 
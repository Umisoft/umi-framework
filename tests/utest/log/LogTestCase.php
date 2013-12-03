<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\log;

use utest\TestCase;

/**
 * Тест кейс логирования
 */
abstract class LogTestCase extends TestCase
{
    use TLogSupport;
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerLogTools();
        parent::setUp();
    }
}
 
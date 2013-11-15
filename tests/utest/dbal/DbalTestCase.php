<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal;

use utest\TestCase;

/**
 * Тест кейс для работы с БД
 */
abstract class DbalTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this
            ->getTestToolkit()
            ->registerToolbox(
                require(LIBRARY_PATH . '/event/toolbox/config.php')
            );

        parent::setUp();
    }
}

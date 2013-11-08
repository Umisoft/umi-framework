<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\unit\pagination\toolbox;

use umi\pagination\toolbox\PaginationTools;
use utest\TestCase;

/**
 * Тесты инструментов пагинатора.
 */
class PaginatorToolsTest extends TestCase
{
    /**
     * @var PaginationTools $tools инструменты пагинатора
     */
    protected $tools;

    public function setUpFixtures()
    {
        $this->tools = new PaginationTools();
        $this->resolveOptionalDependencies($this->tools);
    }

    public function testPaginatorFactory()
    {
        $factory = $this->tools->getPaginatorFactory();

        $this->assertInstanceOf('umi\pagination\IPaginatorFactory', $factory);
    }
}
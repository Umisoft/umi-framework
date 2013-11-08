<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit;

use umi\filter\FilterCollection;
use umi\filter\IFilter;
use utest\TestCase;
use utest\filter\mock\FilterFixture;

/**
 * Тесты коллекций фильтров
 */
class FilterCollectionTests extends TestCase
{

    /**
     * @var IFilter $validCollection
     */
    private $validCollection = null;

    /**
     * @var IFilter $invalidCollection
     */
    private $invalidCollection = null;

    public function setUpFixtures()
    {
        $mockValid = new FilterFixture(['default' => 'mock: ']);

        $mockInvalid = new FilterFixture([]);

        $this->validCollection = new FilterCollection([
            'mock1' => $mockValid,
            'mock2' => $mockValid,
        ]);

        $this->invalidCollection = new FilterCollection([
            'mock1' => $mockValid,
            'mock2' => $mockInvalid,
        ]);
    }

    public function testValidCollection()
    {
        $this->assertEquals(
            "mock: mock: string",
            $this->validCollection->filter("string"),
            "Ожидается, что оба фильтра отработают"
        );
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function invalidCollection()
    {
        $this->invalidCollection->filter("string");
    }
}
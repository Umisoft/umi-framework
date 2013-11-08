<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\unit;

use umi\validation\IValidator;
use umi\validation\ValidatorCollection;
use utest\TestCase;
use utest\validation\mock\ValidatorFixture;

/**
 * Тесты коллекций валидаторов
 */
class ValidatorCollectionTests extends TestCase
{

    /**
     * @var IValidator $validCollection
     */
    private $validCollection = null;

    /**
     * @var IValidator $invalidCollection
     */
    private $invalidCollection = null;

    public function setUpFixtures()
    {
        $mockValid = new ValidatorFixture(['is_valid' => true]);

        $mockInvalid = new ValidatorFixture(['is_valid' => false]);

        $this->validCollection = new ValidatorCollection([
            'mock1' => $mockValid,
            'mock2' => $mockValid,
        ]);

        $this->invalidCollection = new ValidatorCollection([
            'mock1' => $mockInvalid,
            'mock2' => $mockValid,
        ]);
    }

    public function testValidCollection()
    {
        $this->assertTrue(
            $this->validCollection->isValid(true),
            "Ожидается, что коллекция валидаторов пройдет валидацию"
        );
        $this->assertEmpty($this->validCollection->getMessages(), "Ожидается, что сообщений об ошибках не будет");
    }

    public function testInvalidCollection()
    {
        $this->assertFalse(
            $this->invalidCollection->isValid(true),
            "Ожидается, что коллекция валидаторов не пройдет валидацию"
        );
        $this->assertEquals(
            ['Invalid validator'],
            $this->invalidCollection->getMessages(),
            "Ожидается, что будет 1 сообщение об ошибке"
        );
    }
}
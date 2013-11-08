<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\validation\unit\toolbox;

use umi\validation\exception\RuntimeException;
use umi\validation\IValidatorFactory;
use umi\validation\toolbox\factory\ValidatorFactory;
use utest\TestCase;

/**
 * Тесты инструментов валидации
 */
class ValidationFactoryTests extends TestCase
{

    /**
     * @var IValidatorFactory $tools набор инструментов валидации
     */
    protected $tools = null;

    public function setUpFixtures()
    {
        $this->tools = new ValidatorFactory();
        $this->resolveOptionalDependencies($this->tools);

        $this->tools->types = [
            'mock'  => 'utest\validation\mock\ValidatorFixture2',
            'mock2' => 'utest\validation\mock\ValidatorFixture2',
        ];
        $this->tools->validatorCollectionClass = 'utest\validation\mock\ValidatorCollectionFixture';
    }

    public function testValidatorCreation()
    {
        $validator = $this->tools->createValidator('mock', ['settings' => 'test']);
        $this->assertInstanceOf(
            'utest\validation\mock\ValidatorFixture2',
            $validator,
            "Ожидается, что будет создан валидатор"
        );

        $validatorCollection = $this->tools->createValidatorCollection(
            [
                'mock'  => [],
                'mock2' => []
            ]
        );
        $this->assertInstanceOf(
            'utest\validation\mock\ValidatorCollectionFixture',
            $validatorCollection,
            "Ожидается, что будет создана коллекция валидаторов"
        );
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongValidator()
    {
        $this->tools->createValidator('mock3');
    }
}
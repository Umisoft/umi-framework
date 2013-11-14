<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\toolbox;

use umi\session\exception\OutOfBoundsException;
use umi\session\toolbox\factory\SessionEntityFactory;
use utest\session\SessionTestCase;

/**
 * Тесты фабрики пространств имен сессии.
 */
class EntityFactoryTest extends SessionTestCase
{

    /**
     * @var SessionEntityFactory $factory
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = new SessionEntityFactory();
        $this->factory->validatorClasses['mock'] = 'utest\session\mock\validator\MockSessionValidator';
        $this->factory->storageClasses['null'] = 'utest\session\mock\storage\Null';
        $this->resolveOptionalDependencies($this->factory);
    }

    public function testCreateValidator()
    {
        $validator = $this->factory->createSessionValidator('mock', false);
        $this->assertInstanceOf('umi\session\entity\validator\ISessionValidator', $validator);
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongValidatorType()
    {
        $this->factory->createSessionValidator('wrong', false);
    }

    public function testCreateNamespace()
    {
        $ns = $this->factory->createSessionNamespace('nsName');
        $this->assertInstanceOf('umi\session\entity\ns\ISessionNamespace', $ns);

        $this->assertEquals('nsName', $ns->getName());
    }

    public function testCreateStorage()
    {
        $storage = $this->factory->createSessionStorage('null');
        $this->assertInstanceOf('\SessionHandlerInterface', $storage);
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongStorageType()
    {
        $this->factory->createSessionStorage('wrong');
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\storage;

use umi\authentication\storage\SimpleStorage;
use utest\TestCase;

/**
 * Тесты simple storage
 */
class SimpleTest extends TestCase
{

    /**
     * @var SimpleStorage $storage
     */
    protected $storage;

    public function setUpFixtures()
    {
        $this->storage = new SimpleStorage();
    }

    public function testIdentity()
    {
        $this->assertNull($this->storage->getIdentity(), 'Ожидается, что идентификатор будет пуст.');
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать.');

        $this->assertSame(
            $this->storage,
            $this->storage->setIdentity('identity'),
            'Ожидается, что будет возвращен $this.'
        );

        $this->assertEquals('identity', $this->storage->getIdentity(), 'Ожидается, что идентификатор был сохранен.');
        $this->assertTrue($this->storage->hasIdentity(), 'Ожидается, что идентификатор будет существовать.');

        $this->assertSame($this->storage, $this->storage->clearIdentity(), 'Ожидается, что будет возвращен $this.');
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать.');
        $this->assertNull($this->storage->getIdentity(), 'Ожидается, что идентификатор будет пуст.');
    }
}
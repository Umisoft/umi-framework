<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field;

use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;
use utest\TestCase;

/**
 * Базовый кейс для тестирования полей
 */
abstract class FieldTestCase extends TestCase
{

    /**
     * @var IObject $object
     */
    protected $object;

    /**
     * Возвращает поле для тестирования
     * @return IField
     */
    abstract protected function getField();

    public function testInstance()
    {
        $this->assertInstanceOf(
            'umi\orm\metadata\field\BaseField',
            $this->getField(),
            'Ожидается, что поле использует функциоонал базового класса'
        );
    }

    /**
     * Возвращает mock-объект для тестирования
     * @return IObject
     */
    protected function getMockObject()
    {
        if (!$this->object) {
            $this->object = $this->getMock('umi\orm\object\Object', [], [], '', false);
        }

        return $this->object;
    }

}

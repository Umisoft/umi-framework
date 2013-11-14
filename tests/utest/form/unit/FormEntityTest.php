<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\interfaces;

use umi\form\IFormEntity;
use utest\form\FormTestCase;
use utest\form\mock\interfaces\FormEntity;

/**
 * Тесты трейта "Элемент формы".
 */
class TFormEntityTest extends FormTestCase
{
    /**
     * @var IFormEntity $entity элемент
     */
    public $entity;

    public function setUpFixtures()
    {
        $this->entity = new FormEntity('test');
    }

    /**
     * Тестирование аттрибутов.
     */
    public function testAttributes()
    {
        $this->assertInstanceOf(
            '\ArrayObject',
            $this->entity->getAttributes(),
            'Ожидается, что будет возвращен ArrayObject.'
        );
        $this->assertEquals(
            ['name' => 'test'],
            $this->entity->getAttributes()
                ->getArrayCopy(),
            'Ожидается, что устанволен аттрибут с именем.'
        );

        $this->entity->getAttributes()['testAttr'] = 'val';
        $this->assertEquals(
            ['name' => 'test', 'testAttr' => 'val'],
            $this->entity->getAttributes()
                ->getArrayCopy(),
            'Ожидается, что аттрибуты будут установлены.'
        );
    }
}
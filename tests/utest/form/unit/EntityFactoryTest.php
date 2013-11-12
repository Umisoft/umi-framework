<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit;

use umi\form\element\IElement;
use umi\form\exception\OutOfBoundsException;
use umi\form\toolbox\factory\EntityFactory;
use umi\toolkit\factory\TFactory;
use utest\TestCase;

/**
 * Тесты фабрики элементов формы.
 */
class EntityFactoryTest extends TestCase
{
    /**
     * @var EntityFactory $factory фабрика элементов формы.
     */
    public $factory;

    public function setUpFixtures()
    {
        $this->factory = new EntityFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    /**
     * Тест создания элемента формы.
     */
    public function testCreateEntity()
    {
        $this->assertInstanceOf(
            $this->factory->elementTypes['text'],
            $this->factory->createEntity('test', ['type' => 'text']),
            'Ожидается, что будет создан текстовый элемент.'
        );

        $this->assertInstanceOf(
            $this->factory->elementTypes['text'],
            $this->factory->createEntity('test', []),
            'Ожидается, что будет создан текстовый элемент.'
        );

        $this->assertInstanceOf(
            $this->factory->fieldsetTypes['fieldset'],
            $this->factory->createEntity('test', ['elements' => ['test' => ['type' => 'text']]]),
            'Ожидается, что будет создана группа полей.'
        );
    }

    /**
     * @test исключение, если тип элемента не известен.
     * @expectedException OutOfBoundsException
     */
    public function invalidElementType()
    {
        $this->factory->createEntity('test', ['type' => 'NaN']);
    }

    /**
     * Тест создания элементов формы.
     */
    public function testCreateEntities()
    {
        $elements = $this->factory->createEntities(
            [
                'test1' => [],
                'test2' => []
            ]
        );

        $this->assertCount(2, $elements, 'Ожидается, что будет создано 2 элемента.');
        /**
         * @var IElement $element
         */
        $element = $elements['test1'];
        $this->assertInstanceOf(
            $this->factory->elementTypes['text'],
            $element,
            'Ожидается, что будут созданы текстовые элементы.'
        );
        $this->assertEquals('test1', $element->getName(), 'Ожидается, что имя элемента будет установлено.');
    }

    /**
     * Тест создания формы.
     */
    public function testFormCreation()
    {
        $form = $this->factory
            ->createForm(
                [
                    'action' => '/',
                    'elements' => [
                        'test' => []
                    ]
                ]
            );

        $this->assertInstanceOf('umi\form\Form', $form, 'Ожидается, что форма будет создана.');
        $this->assertInstanceOf(
            'umi\form\element\Text',
            $form->getElement('test'),
            'Ожидается, что форма будет содержать элемент.'
        );
    }
}
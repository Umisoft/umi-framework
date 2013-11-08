<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\fieldset;

use umi\form\element\Text;
use umi\form\fieldset\Collection;
use umi\form\Form;
use umi\toolkit\factory\TFactory;
use utest\TestCase;

/**
 * Тесты коллекции элементов.
 */
class CollectionTest extends TestCase
{
    use TFactory;

    /**
     * @var Collection $collection коллекция элементов
     */
    public $collection;

    public function setUpFixtures()
    {
        $this->collection = new Collection('collection', [], [], [
            new Text('element')
        ]);
    }

    /**
     * Тест получения элемента.
     */
    public function testGetElement()
    {
        $el = $this->collection->getEmptyEntity();
        $this->assertInstanceOf('umi\form\element\Text', $el, 'Ожидается, что будет получен элемент.');
        $this->assertEquals('element', $el->getName(), 'Ожидается, что будет получен элемент с заданным имененем.');

        $this->collection->setData(
            [
                'collection' => [
                    'test value 1'
                ]
            ]
        );
        $el = $this->collection->getElement(0);
        $this->assertInstanceOf('umi\form\element\Text', $el, 'Ожидается, что будет получен элемент.');
        $this->assertEquals('element', $el->getName(), 'Ожидается, что будет получен элемент с заданным имененем.');
    }

    /**
     * @test исключения, при попытке получить несуществующий элемент
     * @expectedException \umi\form\exception\OutOfBoundsException
     */
    public function notExistingElement()
    {
        $this->collection->getElement(0);
    }

    /**
     * Тест обходимости коллекции.
     */
    public function testTraversable()
    {
        $this->collection->setData(
            [
                'collection' => ['test value 1', 'test value 2', 'test value 3'],
            ]
        );
        $elements = iterator_to_array($this->collection);
        $this->assertEquals(
            [0, 1, 2],
            array_keys($elements),
            'Ожидается, что группа полей может быть использована в foreach.'
        );
    }

    /**
     * Тест установки и проверки данных в группу полей.
     */
    public function testData()
    {
        $this->assertEquals(
            [
                'collection' => []
            ],
            $this->collection->getData(),
            'Ожидается, что данные не были установлены.'
        );

        $this->assertSame(
            $this->collection,
            $this->collection->setData(
                [
                    'collection' => [
                        'test value 1',
                        'test value 2',
                        'test value 3'
                    ],
                ]
            ),
            'Ожидается, что будет возвращен $this'
        );

        $this->assertTrue($this->collection->isValid(), 'Ожидается, что валидация прошла успешно.');

        $this->assertEquals(
            [
                'collection' => [
                    'test value 1',
                    'test value 2',
                    'test value 3'
                ]
            ],
            $this->collection->getData(),
            'Ожидается, что данные будут установлены и получены.'
        );
    }

    /**
     * @test исключения, при установке неверного элемента
     * @expectedException \umi\form\exception\RuntimeException
     */
    public function wrongTypeElement()
    {
        new Collection('test', [], [], ['Not An Element']);
    }

    /**
     * Тест коллекции групп полей. (вложенные группы полей)
     */
    public function testCollectionOfForms()
    {
        $form = new Form('fieldset', [], [], [
            'element1' => new Text('element1'),
            'element2' => new Text('element2')
        ]);

        $this->collection = new Collection('collection', [], [], [$form]);

        $this->collection->setData(
            [
                'collection' => [
                    ['element1' => 'test value 1', 'element2' => 'test value 2'],
                    ['element1' => 'test value 3', 'element2' => 'test value 4'],
                    ['element1' => 'test value 5']
                ]
            ]
        );

        $this->assertTrue($this->collection->isValid(), 'Ожидается, что валидация прошла успешно.');

        $this->assertEquals(
            [
                'collection' => [
                    ['element1' => 'test value 1', 'element2' => 'test value 2'],
                    ['element1' => 'test value 3', 'element2' => 'test value 4'],
                    ['element1' => 'test value 5', 'element2' => null],
                ]
            ],
            $this->collection->getData(),
            'Ожидается, что данные будут установлены и получены.'
        );
    }

    /**
     * @test исключения, если передано более одного элемента.
     * @expectedException \umi\form\exception\RuntimeException
     */
    public function tooManyElements()
    {
        new Collection('test', [], [], [
            new Text('element1'),
            new Text('element2')
        ]);
    }

    /**
     * @test исключения, если не передано ни одного элемента.
     * @expectedException \umi\form\exception\RuntimeException
     */
    public function noDefaultElement()
    {
        new Collection('test', [], [], []);
    }
}
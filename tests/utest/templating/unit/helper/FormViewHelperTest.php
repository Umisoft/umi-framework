<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper;

use umi\form\IForm;
use umi\form\toolbox\factory\EntityFactory;
use umi\templating\exception\InvalidArgumentException;
use umi\templating\extension\helper\type\form\FormHelper;
use umi\templating\extension\helper\type\form\FormHelper;
use utest\templating\TemplatingTestCase;

/**
 * Тесты помощников вида для форм.
 */
class FormHelperTest extends TemplatingTestCase
{
    /**
     * @var IForm $form
     */
    protected $form;
    /**
     * @var FormHelper $helper
     */
    protected $helper;

    public function setUpFixtures()
    {
        $this->helper = new FormHelper();
        $entityFactory = new EntityFactory();
        $this->resolveOptionalDependencies($entityFactory);

        $this->form = $entityFactory
            ->createForm(
            [
                'name'       => 'contact',
                'action'     => '/contact',
                'method'     => 'post',
                'attributes' => [
                    'class' => 'form-horizontal'
                ],
                'elements'   => [
                    'input'    => [
                        'type'       => 'hidden',
                        'attributes' => [
                            'data-id' => '123'
                        ]
                    ],
                    'textarea' => [
                        'type'       => 'textarea',
                        'attributes' => [
                            'data-id' => '321'
                        ]
                    ],
                    'select'   => [
                        'type'    => 'select',
                        'options' => [
                            'choices' => [
                                'val1' => 'Label 1',
                                'val2' => 'Label 2'
                            ]
                        ]
                    ],
                    'checkbox' => [
                        'type'       => 'checkbox',
                        'attributes' => [
                            'data-id' => '111'
                        ]
                    ],
                    'button'   => [
                        'type'       => 'button',
                        'label'      => 'Label',
                        'attributes' => [
                            'data-id' => '222'
                        ]
                    ],
                ]
            ]
        );
    }

    /**
     * @return FormHelper
     */
    protected function getFormHelperCollection()
    {
        $helper = $this->helper;

        return $helper();
    }

    public function testBasic()
    {
        $this->assertInstanceOf(
            'umi\templating\extension\helper\type\form\FormHelperCollection',
            $this->getFormHelperCollection(),
            'Ожидается, что вернется коллекция помощников вида.'
        );
    }

    public function testOpenAndCloseTag()
    {
        $this->assertEquals(
            '<form name="contact" class="form-horizontal" method="post" action="/contact">',
            $this->getFormHelperCollection()
                ->openTag($this->form),
            'Ожидается, что будет получен открывающий тэг формы.'
        );

        $this->assertEquals(
            '</form>',
            $this->getFormHelperCollection()
                ->closeTag(),
            'Ожидается, что будет получен закрывающий тэг формы.'
        );
    }

    public function testInput()
    {
        $this->assertEquals(
            '<input name="input" data-id="123" type="hidden" value="" />',
            $this->getFormHelperCollection()
                ->formInput($this->form->getElement('input')),
            'Ожидается, что будет получен <input> элемент формы.'
        );
    }

    public function testTextarea()
    {
        $this->assertEquals(
            '<textarea name="textarea" data-id="321"></textarea>',
            $this->getFormHelperCollection()
                ->formTextarea($this->form->getElement('textarea')),
            'Ожидается, что будет получен <textarea> элемент формы.'
        );
    }

    public function testSelect()
    {
        $this->assertEquals(
            '<select name="select" value=""><option value="val1">Label 1</option><option value="val2">Label 2</option></select>',
            $this->getFormHelperCollection()
                ->formSelect($this->form->getElement('select')),
            'Ожидается, что будет получен <select> элемент формы.'
        );
    }

    public function testElement()
    {
        $helpers = $this->getFormHelperCollection();
        $this->assertEquals(
            $helpers->formInput($this->form->getElement('input')),
            $helpers->formElement($this->form->getElement('input')),
            'Ожидается, что будет вызван верный Helper.'
        );

        $this->assertEquals(
            $helpers->formSelect($this->form->getElement('select')),
            $helpers->formElement($this->form->getElement('select')),
            'Ожидается, что будет вызван верный Helper.'
        );

        $this->assertEquals(
            $helpers->formTextarea($this->form->getElement('textarea')),
            $helpers->formElement($this->form->getElement('textarea')),
            'Ожидается, что будет вызван верный Helper.'
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function buttonIsNotSupported()
    {
        $helpers = $this->getFormHelperCollection();
        $helpers->formElement($this->form->getElement('button'));
    }

}
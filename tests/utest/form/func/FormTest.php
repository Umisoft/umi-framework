<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\func;

use umi\event\TEventObservant;
use umi\form\IForm;
use utest\TestCase;
use utest\form\mock\binding\BindObject;

/**
 * Тестирование форм.
 */
class FormTest extends TestCase
{
    /**
     * @var IForm $form форма
     */
    protected $form;

    public function setUpFixtures()
    {
        $this->form = $this->getTestToolkit()
            ->getService('umi\form\IEntityFactory')
                ->createForm(require __DIR__ . '/form.php');
    }

    /**
     * Тестирование получения элементов из формы.
     */
    public function testBasic()
    {
        $this->assertEquals(
            'register',
            $this->form->getName(),
            'Ожидается, что имя формы будет установлено.'
        );

        $this->assertInstanceOf(
            'umi\form\element\IElement',
            $this->form->getElement('email'),
            'Ожидается, что будет получен элемент формы.'
        );
        $this->assertInstanceOf(
            'umi\form\fieldset\IFieldset',
            $this->form->getElement('passport'),
            'Ожидается, что будет получена группа полей.'
        );

        $city = $this->form->getElement('passport')
            ->getElement('birthday_city');
        $this->assertInstanceOf(
            'umi\form\element\IElement',
            $city,
            'Ожидается, что будет получен элемент формы.'
        );

        //$this->assertEquals('Санкт-Петербург', $city->getValue(),
        //	'Ожидается, что установлено значение по умолчанию.');

        $submit = $this->form->getElement('submit');
        $this->assertEquals(
            $submit->getLabel(),
            $this->form->getElement('submit')
                ->getValue(),
            'Ожидается, что значение кнопки совпадает с ее названием.'
        );
    }

    /**
     * Тестирует поведение подформ на форме.
     */
    public function testSubForms()
    {
        $city = $this->form->getElement('passport')
            ->getElement('birthday_city');

        $this->assertEquals(
            'passport[birthday_city]',
            $city->getAttributes()['name'],
            'Ожидается, что аттрибут "name" не совпадает с именем.'
        );

        $this->form->setData(
            [
                'passport' => [
                    'number'        => 123456,
                    'birthday_city' => 'Спб'
                ]
            ]
        );

        $this->assertEquals(
            [
                'number'        => 123456,
                'birthday_city' => 'Спб'
            ],
            $this->form->getData()['passport'],
            'Ожидается, что данные были установлены'
        );
    }

    /**
     * Тестирование установки данных в форму.
     */
    public function testFormsData()
    {
        $this->assertSame(
            $this->form,
            $this->form->setData(
                [
                    'email'    => 'name@example.com',
                    'password' => 'password'
                ]
            ),
            'Ожидается, что будет возвращен $this.'
        );

        $this->assertTrue(
            $this->form->isValid(),
            'Ожидается, что неполные данные формы будут верны, т.к. все обязательные элементы установлены.'
        );

        $this->assertEquals(
            [
                'email'           => 'name@example.com',
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => '', //'Санкт-Петербург'
                ],
                'fieldInFieldset' => '',
                'scans'           => [],
            ],
            $this->form->getData(),
            'Ожидается, что будут получены полные данные от формы.'
        );

        /*$this->assertEquals(
            'Санкт-Петербург',
            $this->form->getElement('passport')->getElement('birthday_city')->getValue(),
            'Ожидается, что значение по умолчанию не будет изменено.'
        );*/

        $rawData = [
            'email'           => 'username@example.com',
            'password'        => 'password',
            'passport'        => [
                'number'        => '00123456',
                'birthday_city' => 'Мск', //'Санкт-Петербург'
            ],
            'fieldInFieldset' => 'test',
            'scans'           => [
                'file1',
                'file2',
                'file3'
            ]
        ];
        $this->form->setData($rawData);

        $this->assertTrue($this->form->isValid(), 'Ожидается, что данные формы верны.');
        $this->assertEquals(
            [
                'email'           => 'username@example.com',
                'password'        => 'password',
                'passport'        => [
                    'number'        => '00123456',
                    'birthday_city' => 'Мск'
                ],
                'fieldInFieldset' => 'test',
                'scans'           => [
                    'file1',
                    'file2',
                    'file3'
                ],
            ],
            $this->form->getData(),
            'Ожидается, что будут получены установленные данные'
        );
    }

    public function testFormValidation()
    {
        $this->form->setData(
            [
                'email'    => 'name',
                'password' => 'password'
            ]
        );

        $this->assertFalse($this->form->isValid(), 'Ожидается, что данные не пройдут валидацию.');
        $this->assertEquals(
            [
                'email'           => null,
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => '', //'Санкт-Петербург'
                ],
                'fieldInFieldset' => '',
                'scans'           => [],
            ],
            $this->form->getData(),
            'Ожидается, что будут получены данные прошедшие валидацию.'
        );

        $this->form->setData(
            [
                'email'    => '              name@example.ru',
                'password' => 'password'
            ]
        );

        $this->assertTrue($this->form->isValid(), 'Ожидается, что данные пройдут валидацию.');
        $this->assertEquals(
            [
                'email'           => 'name@example.ru',
                'password'        => 'password',
                'passport'        => [
                    'number'        => null,
                    'birthday_city' => '', //'Санкт-Петербург'
                ],
                'fieldInFieldset' => '',
                'scans'           => [],
            ],
            $this->form->getData(),
            'Ожидается, что будут получены данные прошедшие фильтрацию.'
        );
    }

    /**
     * Тестирование биндинга.
     */
    public function testBinding()
    {
        $bindObject = new BindObject();
        $bindObject->email = 'test@email.ru';

        $this->form->bindObject($bindObject);
        $this->assertEquals(
            $bindObject->email,
            $this->form->getElement('email')
                ->getValue(),
            'Ожидается, что данные из объекта будут установлены в форму.'
        );

        $this->form->setData(
            [
                'email'    => 'name@example.com',
                'password' => 'password'
            ]
        );

        $this->assertEquals(
            'name@example.com',
            $bindObject->email,
            'Ожидается, что данные из формы будут установлены в объект.'
        );

        $bindObject->email = 'test2';
        $this->assertEquals(
            $bindObject->email,
            $this->form->getElement('email')
                ->getValue(),
            'Ожидается, что данные из объекта будут установлены в форму.'
        );
    }
}
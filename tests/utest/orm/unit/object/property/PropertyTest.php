<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\object\property;

use umi\orm\metadata\field\string\TextField;
use umi\orm\object\IObject;
use umi\orm\object\property\Property;
use utest\orm\ORMTestCase;

/**
 * Тесты для обычного свойства
 */
class PropertyTest extends ORMTestCase
{

    /**
     * @var Property $commonProperty
     */
    protected $commonProperty;

    /**
     * Заглушка для IObject::fullyLoad()
     * @return bool
     */
    public function mockFullyLoad()
    {
    }

    protected function setUpFixtures()
    {
        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', ['fullyLoad'], [], '', false);

        $object->expects($this->any())
            ->method('fullyLoad')
            ->will($this->returnCallback([$this, 'mockFullyLoad']));
        $this->resolveOptionalDependencies($object);

        $commonField = new TextField('login', ['accessor' => 'getLogin', 'mutator' => 'setLogin']);
        $this->commonProperty = new Property($object, $commonField);
    }

    public function testPropertyMethods()
    {

        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $this->commonProperty->getField(),
            'Ожидается, что IProperty::getField() вернет IField'
        );
        $this->assertEquals(
            'login',
            $this->commonProperty->getFullName(),
            'Ожидается, что у не локализованного свойства полное имя не содержит имя локали'
        );
        $this->assertEquals(
            'getLogin',
            $this->commonProperty->getAccessor(),
            'Ожидается, что у свойства login есть прямой метод для получения значения'
        );
        $this->assertEquals(
            'setLogin',
            $this->commonProperty->getMutator(),
            'Ожидается, что у свойства login есть прямой метод для установки значения'
        );

    }

    public function testPropertyInitialState()
    {
        $this->assertFalse($this->commonProperty->getIsLoaded(), 'Ожидается, что свойство по умолчанию не загружено');
        $this->assertFalse($this->commonProperty->getIsModified(), 'Ожидается, что свойство по умолчанию не изменено');
        $this->assertFalse(
            $this->commonProperty->getIsValuePrepared(),
            'Ожидается, что свойство по умолчанию подготовлено'
        );

        $this->assertNull(
            $this->commonProperty->getValue(),
            'Ожидается, что свойство без значения по умолчанию не имеет никаких значений'
        );
        $this->assertNull(
            $this->commonProperty->getDbValue(),
            'Ожидается, что свойство без значения по умолчанию не имеет никаких значений'
        );
        $this->assertNull(
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что свойство без значения по умолчанию не имеет никаких значений'
        );
        $this->assertNull(
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что свойство без значения по умолчанию не имеет никаких значений'
        );
    }

    public function testPropertyAfterSetInitialValueState()
    {
        $this->commonProperty->setInitialValue('test_login');
        $this->assertTrue(
            $this->commonProperty->getIsLoaded(),
            'Ожидается, что после выставления начального значения свойство загружено'
        );
        $this->assertFalse(
            $this->commonProperty->getIsModified(),
            'Ожидается, что после выставления начального значения свойство не считается модифицированным'
        );
        $this->assertFalse(
            $this->commonProperty->getIsValuePrepared(),
            'Ожидается, что после выставления начального значения свойство не считается подготовленным'
        );

        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что после выставления начального значения свойства test_login '
            . 'это значение также становится старым значением'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getValue(),
            'Ожидается, что что после выставления начального значения свойство имеет значение test_login'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getDbValue(),
            'Ожидается, что после выставления начального значения свойство имеет внутренне значение test_login'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что после выставления начального значения свойства test_login '
            . 'выставляется соответсвующее внутреннее старое значение'
        );
    }

    public function testPropertyAfterSetValueState()
    {

        $this->commonProperty->setInitialValue('test_login');
        $this->commonProperty->setValue('new_login');

        $this->assertTrue(
            $this->commonProperty->getIsLoaded(),
            'Ожидается, что установка значения вызывает событие на загрузку свойства'
        );
        $this->assertTrue(
            $this->commonProperty->getIsModified(),
            'Ожидается, что после выставления значения отличного от начального свойство считается модифицированным'
        );
        $this->assertTrue(
            $this->commonProperty->getIsValuePrepared(),
            'Ожидается, что после выставления значения свойство считается подготовленным'
        );

        $this->assertEquals(
            'new_login',
            $this->commonProperty->getValue(),
            'Ожидается, что что после выставления значения свойство имеет значение test_login'
        );
        $this->assertEquals(
            'new_login',
            $this->commonProperty->getDbValue(),
            'Ожидается, что после выставления значения свойство имеет внутренне значение test_login'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что после выставления значения свойства new_login старое значение равно test_login'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что после выставления значения свойства new_login внутренне старое значение равно test_login'
        );

        $this->commonProperty->setIsConsistent();
        $this->assertEquals(
            'new_login',
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что после сброса флага модифицированности старое значение свойства принимает текущее значение'
        );
        $this->assertEquals(
            'new_login',
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что после сброса флага модифицированности старое значение свойства принимает текущее значение'
        );

    }

    public function testNullInitialValue()
    {
        $this->commonProperty->setValue('new_login');
        $this->assertNull(
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что старое значение null, так как не было задано изначальное значение'
        );
        $this->assertNull(
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что старое значение null, так как не было задано изначальное значение'
        );
    }

    public function testModifyProperty()
    {

        $this->commonProperty->setInitialValue('test_login');
        $this->commonProperty->setValue('test_login');

        $this->assertTrue(
            $this->commonProperty->getIsLoaded(),
            'Ожидается, что установка значения вызывает событие на загрузку свойства'
        );
        $this->assertTrue(
            $this->commonProperty->getIsValuePrepared(),
            'Ожидается, что после выставления значения равного начальному свойство считается подготовленным'
        );
        $this->assertFalse(
            $this->commonProperty->getIsModified(),
            'Ожидается, что после выставления значения равного начальному свойство считается не модифицированным'
        );

        $this->commonProperty->setValue('new_test_login');
        $this->commonProperty->setValue('new_new_test_login');

        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что при повторном изменении значения свойства старое значение равно начальному значению'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что при повторном изменении значения свойства '
            . 'старое внутреннее значение равно начальному значению'
        );

        $this->commonProperty->setInitialValue('test_login1');

        $this->assertTrue(
            $this->commonProperty->getIsLoaded(),
            'Ожидается, что после выставления начального значения свойство загружено'
        );
        $this->assertFalse(
            $this->commonProperty->getIsModified(),
            'Ожидается, что после выставления начального значения свойство не считается модифицированным'
        );
        $this->assertFalse(
            $this->commonProperty->getIsValuePrepared(),
            'Ожидается, что после выставления начального значения свойство не считается подготовленным'
        );

        $this->assertEquals(
            'test_login1',
            $this->commonProperty->getValue(),
            'Ожидается, что после выставления начального значения свойство имеет значение test_login1'
        );
        $this->assertEquals(
            'test_login1',
            $this->commonProperty->getDbValue(),
            'Ожидается, что после выставления начального значения свойство имеет внутренне значение test_login1'
        );
        $this->assertEquals(
            'test_login1',
            $this->commonProperty->getPreviousValue(),
            'Ожидается, что после выставления начального значения свойства test_login1 '
            . 'это значение также становится старым значением'
        );
        $this->assertEquals(
            'test_login1',
            $this->commonProperty->getPreviousDbValue(),
            'Ожидается, что после выставления начального значения свойства test_login1 '
            . 'выставляется соответсвующее внутреннее старое значение'
        );
    }

    public function testRollback()
    {
        $this->commonProperty->setInitialValue('test_login');
        $this->commonProperty->setValue('new_test_login');

        $this->commonProperty->rollback();
        $this->assertFalse(
            $this->commonProperty->getIsModified(),
            'Ожидается, что после отката свойство не считается модифицированным'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getValue(),
            'Ожидается, что после отката свойство имеет значение test_login'
        );
        $this->assertEquals(
            'test_login',
            $this->commonProperty->getDbValue(),
            'Ожидается, что после отката свойство имеет внутренне значение test_login'
        );
    }

    public function testImpossibleSetValue()
    {
        $this->commonProperty->setInitialValue('login');
        $e = null;
        try {
            $this->commonProperty->setValue(['login']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выставить для свойства значение, не соответвующее типу свойства'
        );
    }
}

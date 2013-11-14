<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\annotation;

use umi\form\annotation\RequiredAnnotation;
use umi\form\annotation\ValidatorAnnotation;
use umi\form\element\Text;
use utest\form\FormTestCase;

class RequiredAnnotationTest extends FormTestCase
{
    /**
     * @var ValidatorAnnotation $annotation
     */
    protected $annotation;

    public function setUpFixtures()
    {
        $this->annotation = new RequiredAnnotation(true);

        $this->resolveOptionalDependencies($this->annotation);
    }

    public function testBasic()
    {
        $element = new Text('test');
        $this->resolveOptionalDependencies($element);

        $element->setValue(null);
        $this->assertTrue(
            $element->isValid(),
            'Ожидается, что значение не будет провалидированно.'
        );

        $this->annotation->transform($element);

        $attributes = $element->getAttributes();
        $this->assertEquals('required', isset($attributes['required']) ? $attributes['required'] : null);

        $element->setValue(null);
        $this->assertFalse(
            $element->isValid(),
            'Ожидается, что значение не будет верным.'
        );
    }

}
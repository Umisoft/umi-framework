<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\annotation;

use umi\form\annotation\ValidatorAnnotation;
use umi\form\element\Text;
use umi\validation\IValidatorFactory;
use utest\TestCase;

class ValidatorAnnotationTest extends TestCase
{
    /**
     * @var ValidatorAnnotation $annotation
     */
    protected $annotation;

    public function setUpFixtures()
    {
        $this->annotation = new ValidatorAnnotation([
            IValidatorFactory::TYPE_EMAIL => []
        ]);

        $this->resolveOptionalDependencies($this->annotation);
    }

    public function testBasic()
    {
        $element = new Text('test');
        $this->resolveOptionalDependencies($element);

        $element->setValue('test');
        $this->assertTrue(
            $element->isValid(),
            'Ожидается, что значение не будет провалидированно.'
        );

        $this->annotation->transform($element);

        $element->setValue('test');
        $this->assertFalse(
            $element->isValid(),
            'Ожидается, что значение не будет верным.'
        );
    }

}
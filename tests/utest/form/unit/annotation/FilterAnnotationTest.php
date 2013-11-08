<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\annotation;

use umi\filter\IFilterFactory;
use umi\form\annotation\FilterAnnotation;
use umi\form\element\Text;
use utest\TestCase;

class FilterAnnotationTest extends TestCase
{
    /**
     * @var FilterAnnotation $annotation
     */
    protected $annotation;

    public function setUpFixtures()
    {
        $this->annotation = new FilterAnnotation([
            IFilterFactory::TYPE_INT => []
        ]);

        $this->resolveOptionalDependencies($this->annotation);
    }

    public function testBasic()
    {
        $element = new Text('test');
        $this->resolveOptionalDependencies($element);

        $element->setValue('1a');
        $this->assertEquals(
            '1a',
            $element->getValue(),
            'Ожидается, что будет получено неотфильтрованное значение.'
        );

        $this->annotation->transform($element);

        $element->setValue('1a');
        $this->assertEquals(
            1,
            $element->getValue(),
            'Ожидается, что будет получено отфильтрованное значение.'
        );
    }

}
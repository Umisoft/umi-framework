<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\annotation;

use umi\form\annotation\LabelAnnotation;
use umi\form\element\Text;
use utest\form\FormTestCase;

class LabelAnnotationTest extends FormTestCase
{
    /**
     * @var LabelAnnotation $annotation
     */
    protected $annotation;

    public function setUpFixtures()
    {
        $this->annotation = new LabelAnnotation('label');
    }

    public function testBasic()
    {
        $element = new Text('test');

        $this->assertEmpty($element->getLabel());
        $this->annotation->transform($element);
        $this->assertEquals('label', $element->getLabel());
    }

}
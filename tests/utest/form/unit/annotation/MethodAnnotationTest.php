<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\annotation;

use umi\form\annotation\MethodAnnotation;
use umi\form\Form;
use utest\TestCase;

class MethodAnnotationTest extends TestCase
{
    /**
     * @var MethodAnnotation $annotation
     */
    protected $annotation;

    public function setUpFixtures()
    {
        $this->annotation = new MethodAnnotation('post');
    }

    public function testBasic()
    {
        $form = new Form('test');

        $this->assertEquals('get', $form->getMethod());
        $this->annotation->transform($form);
        $this->assertEquals('post', $form->getMethod());
    }

}
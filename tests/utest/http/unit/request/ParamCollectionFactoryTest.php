<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\request;

use umi\http\toolbox\factory\ParamCollectionFactory;
use umi\toolkit\exception\DomainException;
use utest\TestCase;

class ParamCollectionFactoryTest extends TestCase
{
    /**
     * @var ParamCollectionFactory $paramCollectionFactory
     */
    private $paramCollectionFactory;

    public function setUpFixtures()
    {
        $this->paramCollectionFactory = new ParamCollectionFactory();
        $this->resolveOptionalDependencies($this->paramCollectionFactory);
    }

    /**
     * @test отсутвия класса для обязательного типа контейнера
     * @expectedException DomainException
     */
    public function wrongParamClass()
    {
        $this->paramCollectionFactory->paramCollectionClass = '\StdClass';
        $a = [];
        $this->paramCollectionFactory->createParamCollection($a);
    }

    public function testCreateParamCollection()
    {
        $a = [
            'test' => 'test'
        ];
        $pc = $this->paramCollectionFactory->createParamCollection($a);
        $this->assertSame($a, $pc->toArray());
    }

}
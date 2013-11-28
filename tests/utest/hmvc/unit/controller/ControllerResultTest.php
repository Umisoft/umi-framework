<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\hmvc\unit\controller;

use umi\hmvc\component\response\model\DisplayModel;
use umi\hmvc\component\response\model\IDisplayModel;
use utest\TestCase;

/**
 * Class ControllerResultTest
 */
class ControllerResultTest extends TestCase
{
    /**
     * @var IDisplayModel $result
     */
    protected $result;

    public function setUpFixtures()
    {
        $this->result = new DisplayModel('template', ['variable' => 'value'], 300);
    }

    public function testBasic()
    {
        $this->assertEquals('template', $this->result->getTemplate());

        $this->assertEquals(['variable' => 'value'], $this->result->getVariables());
        $this->assertSame($this->result, $this->result->setVariables(['test' => 'val']));
        $this->assertEquals(['test' => 'val'], $this->result->getVariables());

        $this->assertEquals(300, $this->result->getCode());
        $this->assertSame($this->result, $this->result->setCode(200));
        $this->assertEquals(200, $this->result->getCode());
    }

    public function testVariables()
    {
        $this->assertTrue($this->result->has('variable'));
        $this->assertEquals('value', $this->result->get('variable'));

        $this->result->set('variable', 'customValue');
        $this->assertEquals('customValue', $this->result->get('variable'));

        $this->result->del('variable');
        $this->assertFalse($this->result->has('variable'));
        $this->assertNull($this->result->get('variable'));
    }
}

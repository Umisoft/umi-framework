<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\request;

use umi\http\request\param\IParamCollection;
use umi\http\request\param\ParamCollection;
use utest\TestCase;

/**
 * Класс RequestTest
 */
class ParamCollectionTest extends TestCase
{
    /**
     * @var IParamCollection $request
     */
    protected $params = null;
    /**
     * @var array $vars
     */
    protected $vars = [];

    function setUpFixtures()
    {
        $this->params = new ParamCollection($this->vars);
    }

    public function testBasic()
    {
        $this->assertNull($this->params->get('test'), 'Ожидается, что значение не существует.');
        $this->assertFalse($this->params->has('test'), 'Ожидается, что значение не будет найдено.');
        $this->assertEquals(
            'default',
            $this->params->get('test', 'default'),
            'Ожидается, что будет получено значение по умолчанию.'
        );

        $this->assertSame($this->params, $this->params->set('test', 'value'), 'Ожидается, что метод вернет $this');
        $this->assertTrue($this->params->has('test'), 'Ожидается, что значение будет найдено.');
        $this->assertEquals('value', $this->params->get('test'), 'Ожидается, что будет получено значение.');

        $this->assertSame($this->params, $this->params->del('test'), 'Ожидается, что метод вернет $this');
        $this->assertFalse($this->params->has('test'), 'Ожидается, что значение не будет найдено.');
        $this->assertNull($this->params->get('test'), 'Ожидается, что значение было удалено.');
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\result\RouteResult;
use utest\TestCase;

/**
 * Тесты результата роутера.
 */
class ResultTest extends TestCase
{

    public function testSomeBuild()
    {
        $result = new RouteResult(
            'match1/match2',
            ['param1' => 'value1', 'param2' => 'value2'],
            '/match1/match2',
            '/match3'
        );
        $this->assertEquals('/match3', $result->getUnmatchedUrl(), 'Ожидается, поле результата будет установлено.');
        $this->assertEquals(
            '/match1/match2',
            $result->getMatchedUrl(),
            'Ожидается, поле результата будет установлено.'
        );
        $this->assertEquals(
            ['param1' => 'value1', 'param2' => 'value2'],
            $result->getMatches(),
            'Ожидается, поле результата будет установлено.'
        );
        $this->assertEquals('match1/match2', $result->getName(), 'Ожидается, поле результата будет установлено.');
    }
}
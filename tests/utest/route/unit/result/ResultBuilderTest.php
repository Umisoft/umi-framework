<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\result\IRouteResultBuilder;
use umi\route\result\RouteResultBuilder;
use utest\route\RouteTestCase;

/**
 * Тесты билдера результата роутинга.
 */
class ResultBuilderTest extends RouteTestCase
{
    /**
     * @var IRouteResultBuilder $resultBuilder билдер результата роутинга.
     */
    protected $resultBuilder;

    public function setUpFixtures()
    {
        $this->resultBuilder = new RouteResultBuilder();
        $this->resolveOptionalDependencies($this->resultBuilder);
    }

    public function testCleanBuild()
    {
        $result = $this->resultBuilder->getResult();
        $this->assertEmpty($result->getUnmatchedUrl(), 'Ожидается, что все поля собраного результата будут пусты.');
        $this->assertEmpty($result->getMatchedUrl(), 'Ожидается, что все поля собраного результата будут пусты.');
        $this->assertEmpty($result->getMatches(), 'Ожидается, что все поля собраного результата будут пусты.');
        $this->assertEmpty($result->getName(), 'Ожидается, что все поля собраного результата будут пусты.');
    }

    public function testSomeBuild()
    {
        $this->resultBuilder->addMatch('match1', ['param1' => 'value1'], '/match1');
        $this->resultBuilder->addMatch('match2', ['param2' => 'value2'], '/match2');
        $this->resultBuilder->setUnmatchedUrl('/match3');

        $result = $this->resultBuilder->getResult();
        $this->assertEquals('/match3', $result->getUnmatchedUrl(), 'Ожидается, поле результата будет установлено.');
        $this->assertEquals(
            '/match1/match2',
            $result->getMatchedUrl(),
            'Ожидается, что совпавшая часть результата будет собрана.'
        );
        $this->assertEquals(
            ['param1' => 'value1', 'param2' => 'value2'],
            $result->getMatches(),
            'Ожидается, что параметры результата будут собраны.'
        );
        $this->assertEquals(
            'match1/match2',
            $result->getName(),
            'Ожидается, что имя собраного результата будет собрано.'
        );
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

use stdClass;

/**
 * Тестовый интерфейс
 */
class TestService extends ServicingMock implements ITestService, MockServicingInterface
{

    public $type;
    public $name = 'DefaultName';
    public $object;
    public $options = [
        'a1' => 1,
        'a2' => 2,
        'a3' => 3,
        'a4' => [
            'a5' => 5,
            'a6' => 6
        ]
    ];
    /**
     * @var IMockService
     */
    public $mockService;

    /**
     * @var stdClass $test
     */
    public $test;

    /**
     * @var ConcreteMockService
     */
    public $concreteMockService;

    public $args = [];

    /**
     * Конструктор
     * @param $type
     * @param IMockService $mockService
     * @param int $reference
     * @param IMockService $referenceMockService
     * @param ConcreteMockService $concreteMockService
     * @param stdClass $test
     */
    public function __construct(
        $type = null,
        IMockService $mockService = null,
        &$reference = 199,
        IMockService &$referenceMockService = null,
        ConcreteMockService $concreteMockService = null,
        stdClass $test = null
    )
    {
        $this->type = $type;
        $this->mockService = $mockService;
        $this->concreteMockService = $concreteMockService;
        $this->test = $test;
        $reference++;
        if ($referenceMockService) {
            $referenceMockService->setName('referenceName');
        }

        $this->args = array_slice(func_get_args(), 7);
    }
}
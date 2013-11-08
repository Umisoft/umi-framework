<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\spl\unit\config;

use umi\spl\config\TConfigSupport;
use utest\TestCase;

class ConfigSupportTest extends TestCase
{
    use TConfigSupport;

    public function testMergeConfigOptions()
    {
        $options = [
            'key1' => 'value1',
            'key2' => [
                'key3'  => 'value3',
                'key4'  => new \ArrayObject([
                        'key5' => 'value5'
                    ]),
                'key10' => new \ArrayObject([
                        'key11' => 'value11'
                    ])
            ]
        ];

        $default = [
            'defaultKey1' => 'defaultValue1',
            'key2'        => [
                'key3'  => 'defaultValue3',
                'key4'  => new \ArrayObject([
                        'key5'        => 'defaultValue5',
                        'defaultKey2' => 'defaultValue2'
                    ]),
                'key20' => new \ArrayObject([
                        'defaultKey21' => 'defaultValue21'
                    ])
            ]
        ];

        $result = $this->diffConfigOptions($options, $default);
        $actualResult = $this->configToArray($result, true);

        $expectedResult = [
            'key1' => 'value1',
            'key2' => [
                'key3'  => 'value3',
                'key4'  => [
                    'key5'        => 'value5',
                    'defaultKey2' => 'defaultValue2'
                ],
                'key20' => [
                    'defaultKey21' => 'defaultValue21'
                ],
                'key10' => [
                    'key11' => 'value11'
                ]
            ]
        ];
        $this->assertEquals($expectedResult, $actualResult, 'Не верный merge конфигураций');
    }
}

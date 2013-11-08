<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\log\unit\type;

use Psr\Log\Test\LoggerInterfaceTest;
use umi\log\type\OutputLogger;

/**
 * Тесты PSR-логгера
 */
class OutputLoggerTest extends LoggerInterfaceTest
{

    public function setUp()
    {
        ob_start();
    }

    public function tearDown()
    {
        ob_end_clean();
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        $logger = new OutputLogger();
        $logger->setMessageFormat('{level} {message}');

        return $logger;
    }

    /**
     * This must return the log messages in order with a simple formatting: "<LOG LEVEL> <MESSAGE>"
     * Example ->error('Foo') would yield "error Foo"
     * @return string[]
     */
    public function getLogs()
    {
        $string = ob_get_contents();

        return explode(PHP_EOL, trim($string));
    }

    public function testContextCanContainAnything()
    {
        $this->markTestSkipped('PHP bug?');
    }
}
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
use umi\log\ILogger;
use umi\log\type\FileLogger;

/**
 * Тесты PSR-логгера
 */
class FileLoggerTest extends LoggerInterfaceTest
{

    private $fsLog = "logger_test.log";

    public function tearDown()
    {
        @unlink(__DIR__ . '/' . $this->fsLog);
    }

    /**
     * @return ILogger
     */
    public function getLogger()
    {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $this->fsLog, "");
        $logger = new FileLogger([
            'filename' => __DIR__ . DIRECTORY_SEPARATOR . $this->fsLog,
        ]);
        $logger->setMessageFormat('{level} {message}');

        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogs()
    {
        return explode(PHP_EOL, trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $this->fsLog)));
    }

    public function testLoggerOptions()
    {
        file_put_contents(__DIR__ . '/' . $this->fsLog, "");

        for ($i = 0; $i < 2; $i++) {
            $logger = new FileLogger([
                'filename' => __DIR__ . DIRECTORY_SEPARATOR . $this->fsLog,
            ]);
            $logger->log(ILogger::LOG_EMERGENCY, 'Test log');
        }
        $this->assertCount(
            2,
            $this->getLogs(),
            'Ожидается 2 записи логов, если логгеры использовали общий файл с дозаписью в него'
        );

        $logger = new FileLogger([
            'filename' => __DIR__ . DIRECTORY_SEPARATOR . $this->fsLog,
            'clearPrevious' => true
        ]);
        $logger->log(ILogger::LOG_EMERGENCY, 'Test log');

        $this->assertCount(
            1,
            $this->getLogs(),
            'Ожидается 1 запись логов, если логгеры использовали общий файл с перезаписью'
        );
    }

    public function testContextCanContainAnything()
    {
        //$this->markTestSkipped('PHP bug?');
    }
}
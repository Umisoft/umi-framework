<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\log\func;

use Psr\Log\LoggerInterface;
use umi\log\toolbox\LogTools;
use utest\TestCase;

/**
 * Тестирование логгеров
 */
class LoggerTest extends TestCase
{

    /**
     * @var LogTools $factory
     */
    protected $tools;
    /**
     * @var string $fsLogFile файл для логгирования
     */
    protected $fsLogFile;

    public function setUpFixtures()
    {
        $this->getTestToolkit()
            ->registerToolbox(
            [
                'name'    => LogTools::NAME,
                'class'        => 'umi\log\toolbox\LogTools',
                'servicingInterfaces' => [
                    'umi\log\ILoggerAware',
                ]
            ]
        );

        $this->tools = $this->getTestToolkit()
            ->getToolbox(LogTools::NAME);
        $this->fsLogFile = __DIR__ . DIRECTORY_SEPARATOR . 'testLog';
    }

    public function testBaseFunctionality()
    {
        $this->tools->type = LogTools::TYPE_FILE;
        $this->tools->messageFormat = "{level} | {message}";
        $this->tools->options = [
            'filename' => $this->fsLogFile,
        ];

        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->tools->getLogger();

        $logger->info(
            'Logged from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $this->assertEquals(
            "info | Logged from testBaseFunctionality" . PHP_EOL,
            file_get_contents($this->fsLogFile),
            "Ожидается, что сообщение запишется в лог"
        );
    }

    public function testMinLogLevel()
    {
        $this->tools->type = LogTools::TYPE_FILE;
        $this->tools->messageFormat = "{level} | {message}";
        $this->tools->minLevel = 'error';
        $this->tools->options = [
            'filename' => $this->fsLogFile,
        ];

        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->tools->getLogger();

        $logger->info(
            'Info from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $logger->error(
            'Error from {function}',
            [
                'function' => __FUNCTION__
            ]
        );

        $this->assertEquals(
            "error | Error from testMinLogLevel" . PHP_EOL,
            file_get_contents($this->fsLogFile),
            "Ожидается, что сообщение запишется в лог"
        );
    }
}
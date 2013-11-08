<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\log\mock;

use umi\log\type\BaseLogger;

/**
 * Класс логгера для тестов
 */
class MockLogger extends BaseLogger
{

    public $logs = [];

    /**
     * {@inheritdoc}
     */
    public function write($level, $message, array $placeholders = [])
    {
        $this->logs[] = $this->interpolate($message, $placeholders);

        return null;
    }

}

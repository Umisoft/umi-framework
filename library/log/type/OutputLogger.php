<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\log\type;

/**
 * Класс логированния с использованием output буффера.
 */
class OutputLogger extends BaseLogger
{

    /**
     * {@inheritdoc}
     */
    protected function write($level, $message)
    {
        echo $message . PHP_EOL;
    }
}
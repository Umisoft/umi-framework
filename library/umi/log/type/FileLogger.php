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
 * Класс логированния с использованием файлов.
 */
class FileLogger extends BaseLogger
{
    /**
     * @var string $filename имя файла для записи логов
     */
    protected $filename;

    /**
     * Конструктор.
     */
    public function __construct(array $options = [])
    {
        $clearPrevious = isset($options['clearPrevious']) ? $options['clearPrevious'] : false;
        $filename = isset($options['filename']) ? $options['filename'] : null;

        if ($clearPrevious && is_file($filename)) {
            @file_put_contents($filename, '');
        }

        $this->filename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    protected function write($level, $message, array $placeholders = [])
    {
        @file_put_contents($this->filename, $message . PHP_EOL, FILE_APPEND);
    }
}
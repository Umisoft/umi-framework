<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io\writer;

use umi\config\entity\IConfigSource;
use umi\config\exception\RuntimeException;

/**
 * Writer PHP конфигурационных файлов.
 */
class PhpFileWriter extends BaseWriter
{

    /**
     * {@inheritdoc}
     */
    public function write(IConfigSource $config)
    {
        $files = $this->getLocalConfigArrays($config);
        foreach ($files as $alias => $source) {
            $filename = $this->getConfigFilename($alias);

            if ($filename === null) {
                throw new RuntimeException($this->translate(
                    'Config "{alias}" does not have local file.',
                    [
                        'alias' => $alias
                    ]
                ));
            }

            if (!$this->writeSource($filename, $source)) {
                throw new RuntimeException($this->translate(
                    'Cannot write configuration file "{alias}"("{file}").',
                    [
                        'alias' => $alias,
                        'file'  => $filename
                    ]
                ));
            };
        }

        $this->saveConfig($config);
    }

    /**
     * Записывает конфигурационный файл.
     * @param string $filename имя файла
     * @param array $source содержимое, в виде массива скалярных значений
     * @return bool true, если файл успешно записан
     */
    protected function writeSource($filename, array $source)
    {
        $source = str_replace(['  ', "\n"], ["\t", "\n\t"], var_export($source, true));
        $source = <<<FILE
<?php
	return $source;
FILE;

        return @file_put_contents($filename, $source) != 0;
    }
}
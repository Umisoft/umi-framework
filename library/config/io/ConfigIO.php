<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io;

use umi\config\cache\IConfigCacheEngineAware;
use umi\config\cache\TConfigCacheEngineAware;
use umi\config\entity\IConfigSource;
use umi\config\exception\InvalidArgumentException;
use umi\config\exception\RuntimeException;
use umi\config\io\reader\IReader;
use umi\config\io\writer\IWriter;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * I/O сервис конфигурации.
 */
class ConfigIO implements IConfigIO, ILocalizable, IConfigCacheEngineAware
{

    use TLocalizable;
    use TConfigCacheEngineAware;

    /**
     * @var array $aliases список зарегистрированных сиволических имен директорий
     */
    protected $aliases = [];
    /**
     * @var IReader $reader
     */
    protected $reader;
    /**
     * @var IWriter $writer
     */
    protected $writer;

    /**
     * Конструктор.
     * @param IReader $reader reader
     * @param IWriter $writer writer
     * @param array $aliases список символических имен
     * @throws InvalidArgumentException если список сиволических имен неверный
     */
    public function __construct(IReader $reader, IWriter $writer, array $aliases = [])
    {
        $this->reader = $reader;
        $this->writer = $writer;

        $this->aliases = $aliases;
        $this->resortAliases();
    }

    /**
     * {@inheritdoc}
     */
    public function registerAlias($alias, $masterDirectory, $localDirectory = null)
    {
        if (isset($this->aliases[$alias])) {
            throw new RuntimeException($this->translate(
                'Alias "{alias}" already registered.',
                ['alias' => $alias]
            ));
        }

        $directories = [$masterDirectory];
        if ($localDirectory) {
            $directories[] = $localDirectory;
        }

        $this->aliases[$alias] = $directories;

        $this->resortAliases();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function read($alias)
    {
        if ($this->hasConfigCacheEngine()) {

            $time = 0;

            foreach ($this->getFilesByAlias($alias) as $file) {
                if (is_file($file) && filemtime($file) > $time) {
                    $time = filemtime($file);
                }
            }

            if ($this->isConfigCacheActual($alias, $time)) {
                return $this->loadConfig($alias);
            }
        }

        $config = $this->reader->read($alias);
        if ($this->hasConfigCacheEngine()) {
            $this->saveConfig($config);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function write(IConfigSource $config)
    {
        $this->writer->write($config);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesByAlias($fileAlias)
    {
        foreach ($this->aliases as $alias => $directories) {
            if (substr($fileAlias, 0, strlen($alias)) === $alias) {
                $filename = substr($fileAlias, strlen($alias));

                foreach ($directories as &$directory) {
                    $directory .= $filename;
                }

                return $directories;
            }
        }

        throw new RuntimeException($this->translate(
            'Cannot resolve alias "{alias}".',
            ['alias' => $fileAlias]
        ));
    }

    /**
     * Пересортировывает символические имена директорий конфигрурации.
     */
    protected function resortAliases()
    {
        uksort(
            $this->aliases,
            function ($a, $b) {
                return substr_count($a, '/') < substr_count($b, '/');
            }
        );
    }
}
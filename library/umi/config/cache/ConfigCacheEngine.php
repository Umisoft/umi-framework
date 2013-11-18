<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\cache;

use umi\config\entity\IConfigSource;
use umi\config\entity\ISeparateConfigSource;
use umi\config\exception\InvalidArgumentException;
use umi\config\exception\RuntimeException;
use umi\config\exception\UnexpectedValueException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\spl\config\TConfigSupport;

/**
 * Кэширующий механизм для конфигурационных файлов.
 * Сохраняет сериализованные конфигурационные файлы в кэш.
 */
class ConfigCacheEngine implements IConfigCacheEngine, ILocalizable
{
    /** Директория с кэшем */
    const OPTION_DIRECTORY = 'directory';

    use TLocalizable;
    use TConfigSupport;

    /**
     * @var string $directory директория с кэшем.
     */
    protected $directory;

    /**
     * Конструктор.
     * @param array|\Traversable $options конфигурация кеша
     * @throws UnexpectedValueException если задана неверная конфигурация
     * @throws InvalidArgumentException если задана неверная конфигурация
     */
    public function __construct($options)
    {
        try {
            $options = $this->configToArray($options);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException('Dictionaries configuration should be an array or Traversable.', 0, $e);
        }

        if (!isset($options[self::OPTION_DIRECTORY])) {
            throw new InvalidArgumentException($this->translate(
                'Option "directory" is required.'
            ));
        }
        $this->directory = $options[self::OPTION_DIRECTORY];
    }

    /**
     * {@inheritdoc}
     */
    public function isActual($alias, $timestamp)
    {
        $file = $this->getAliasFile($alias);

        return file_exists($file) && (filemtime($file) > $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function load($alias)
    {
        $file = $this->getAliasFile($alias);
        if (!file_exists($file)) {
            throw new RuntimeException($this->translate(
                'Config file "{alias}" not cached.',
                ['alias' => $alias]
            ));
        }

        return unserialize(file_get_contents($file));
    }

    /**
     * {@inheritdoc}
     */
    public function save(IConfigSource $config)
    {
        $file = $this->getAliasFile($config->getAlias());
        file_put_contents($file, serialize($config));
        $this->saveSeparateConfig($config);
    }

    /**
     * Возвращает имя файла кэша по его символическому имени.
     * @param string $alias символическое имя
     * @return string
     */
    protected function getAliasFile($alias)
    {
        $alias = str_replace(['~', '/', '\\'], '_', $alias);

        return $this->directory . DIRECTORY_SEPARATOR . $alias;
    }

    /**
     * Кэширует конфигурации в отдельных файлах (Separate configs).
     * @param IConfigSource $config
     */
    protected function saveSeparateConfig(IConfigSource $config)
    {
        $source = $config->getSource();

        array_walk(
            $source,
            function ($value) {
                if ($value instanceof ISeparateConfigSource) {
                    $this->save($value->getSeparateConfig());
                }
            }
        );
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io\reader;

use umi\config\entity\factory\IConfigEntityFactoryAware;
use umi\config\entity\factory\TConfigEntityFactoryAware;
use umi\config\entity\IConfigSource;
use umi\config\entity\ISeparateConfigSource;
use umi\config\entity\value\IConfigValue;
use umi\config\exception\InvalidArgumentException;
use umi\config\exception\RuntimeException;
use umi\config\exception\UnexpectedValueException;
use umi\config\io\IConfigAliasResolverAware;
use umi\config\io\TConfigAliasResolverAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Считыватель конфигурации из PHP файла.
 */
class PhpFileReader implements IReader, ILocalizable, IConfigAliasResolverAware, IConfigEntityFactoryAware
{

    use TLocalizable;
    use TConfigAliasResolverAware;
    use TConfigEntityFactoryAware;

    /**
     * {@inheritdoc}
     */
    public function read($configAlias)
    {
        $files = $this->getFilesByAlias($configAlias);

        $masterFilename = isset($files[0]) ? $files[0] : null;
        $localFilename = isset($files[1]) ? $files[1] : null;

        if (!file_exists($masterFilename)) {
            throw new RuntimeException($this->translate(
                'Master configuration file "{file}" not found.',
                [
                    'file' => $masterFilename
                ]
            ));
        }

        $config = require $masterFilename;

        array_walk_recursive(
            $config,
            function (&$v) {
                $value = $this->createEntity($v);
                $v = $value;
            }
        );

        if (file_exists($localFilename)) {
            $localSource = require $localFilename;

            $this->mergeConfig($config, $localSource);
        }

        return $this->createConfigSource($configAlias, $config);
    }

    /**
     * Выполняет слияние мастер и локальной конфигурации.
     * @param array $master мастер конфигурация
     * @param array $local локальная конфигурация
     * @throws \Exception
     */
    protected function mergeConfig(array &$master, array &$local)
    {
        foreach ($local as $key => &$localValue) {
            if (isset($master[$key])) {
                $masterValue = & $master[$key];
                if (is_array($masterValue)) {
                    if (!is_array($localValue)) {
                        throw new UnexpectedValueException($this->translate(
                            'Local property "{key}" should be array.',
                            [
                                'key' => $key
                            ]
                        ));
                    }

                    $this->mergeConfig($masterValue, $localValue);
                } elseif ($masterValue instanceof IConfigValue) {
                    try {
                        $masterValue->set($localValue, IConfigValue::KEY_LOCAL)
                            ->save();
                    } catch (InvalidArgumentException $e) {
                        throw new UnexpectedValueException('Local property "{key}" should be scalar.', 0, $e);
                    }
                } else {
                    throw new UnexpectedValueException($this->translate(
                        'Unexpected "{key}" property type.',
                        [
                            'key' => $key
                        ]
                    ));
                }
            } else {
                if (is_array($localValue)) {
                    array_walk_recursive(
                        $localValue,
                        function (&$v) {
                            $v = $this->createConfigValue()
                                ->set($v, IConfigValue::KEY_LOCAL)
                                ->save();
                        }
                    );

                    $master[$key] = $localValue;
                } else {
                    $master[$key] = $this->createConfigValue()
                        ->set($localValue, IConfigValue::KEY_LOCAL)
                        ->save();
                }
            }
        }
    }

    /**
     * Создает сущности на основе конфигурации.
     * @param string $masterValue значение
     * @throws UnexpectedValueException если значение не скалярное
     * @return IConfigSource|ISeparateConfigSource|IConfigValue
     */
    protected function createEntity($masterValue)
    {
        if (!is_scalar($masterValue)) {
            throw new UnexpectedValueException($this->translate(
                'Unexpected configuration value. Configuration can contain only scalar values.'
            ));
        }
        if (preg_match('/^{#(\S+):(.+)}$/', $masterValue, $matches)) {
            list(, $command, $value) = $matches;

            switch ($command) {
                case self::COMMAND_PART:
                    // TODO: подумать, мб через config factory?
                    return $this->read($value);
                case self::COMMAND_LAZY:
                    return $this->createSeparateConfigSource('lazy', $value);
            }
        }

        return $this->createConfigValue()
            ->set($masterValue, IConfigValue::KEY_MASTER)
            ->save();
    }
}
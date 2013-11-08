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
use umi\config\entity\value\IConfigValue;
use umi\config\exception\UnexpectedValueException;
use umi\config\io\IConfigAliasResolverAware;
use umi\config\io\TConfigAliasResolverAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый класс Writer'а конфигурации.
 * Реализует методы получения имени файла для конфигурации и сохранения значений конфигурации.
 */
abstract class BaseWriter implements IWriter, IConfigAliasResolverAware, ILocalizable
{

    use TConfigAliasResolverAware;
    use TLocalizable;

    /**
     * Возвращает имя локального конфигурационного файла для символического имени конфигурации.
     * @param string $alias символическое имя
     * @return string имя файла
     */
    protected function getConfigFilename($alias)
    {
        $files = $this->getFilesByAlias($alias);

        return isset($files[1]) ? $files[1] : null;
    }

    /**
     * Сохраняет сессионные значения конфигурации.
     * @param IConfigSource $config конфигурация
     * @return void
     */
    protected function saveConfig(IConfigSource $config)
    {
        $source = $config->getSource();

        array_walk_recursive(
            $source,
            function (&$value) {
                if ($value instanceof IConfigValue) {
                    $value->save();
                } elseif ($value instanceof IConfigSource) {
                    $this->saveConfig($value);
                } else {
                    throw new UnexpectedValueException($this->translate(
                        'Config source is corrupted.'
                    ));
                }
            }
        );
    }

    /**
     * Возвращает список конфигурационных файлов в виде:
     * [alias => source, ...].
     * @param IConfigSource $config конфигурация
     * @return array
     */
    protected function getLocalConfigArrays(IConfigSource $config)
    {
        $src = $config->getSource();

        $result = [
            $config->getAlias() => $this->convertToArray($src)
        ];

        array_walk_recursive(
            $src,
            function ($value) use (&$result) {
                if ($value instanceof IConfigSource) {
                    $result[$value->getAlias()] = $this->convertToArray($value->getSource());
                }
            }
        );

        return $result;
    }

    /**
     * Конвертирует конфигурацию в массив значений.
     * Конвертация происходит непосредственно с данной конфигурацией и ни с какими более.
     * @param array $config конфигурация
     * @return array
     * @throws UnexpectedValueException если конфигурация повреждена
     */
    private function convertToArray(array $config)
    {
        foreach ($config as $key => &$value) {
            if (is_array($value)) {
                $value = $this->convertToArray($value);

                if (!$value) {
                    unset($config[$key]);
                }
            } elseif ($value instanceof IConfigValue) {
                if ($value->has(IConfigValue::KEY_LOCAL)) {
                    $value = $value->get(IConfigValue::KEY_LOCAL);
                } else {
                    unset($config[$key]);
                }
            } elseif ($value instanceof IConfigSource) {
                unset($config[$key]);
            } else {
                throw new UnexpectedValueException($this->translate(
                    'Config source is corrupted.'
                ));
            }
        }

        return $config;
    }
}
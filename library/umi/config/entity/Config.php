<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

use umi\config\entity\factory\IConfigEntityFactoryAware;
use umi\config\entity\factory\TConfigEntityFactoryAware;
use umi\config\entity\value\ConfigValue;
use umi\config\entity\value\IConfigValue;
use umi\config\exception\InvalidArgumentException;
use umi\config\exception\UnexpectedValueException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\spl\container\TArrayAccess;

/**
 * Конфигурация.
 */
class Config implements IConfig, ILocalizable, IConfigEntityFactoryAware
{

    use TLocalizable;
    use TArrayAccess;
    use TConfigEntityFactoryAware;

    /**
     * @var array $source данные конфигурации
     */
    protected $source = [];

    /**
     * Конструктор.
     * @param array $source данные конфигурации
     */
    public function __construct(array &$source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function get($path)
    {
        return $this->getByPath(explode(self::PATH_SEPARATOR, $path), $this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        array_walk_recursive(
            $this->source,
            function (&$value) {
                $value = clone $value;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function set($path, $newValue)
    {
        $this->setByPath(explode(self::PATH_SEPARATOR, $path), $newValue, $this->source);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $src = $this->source;
        array_walk_recursive(
            $src,
            function (&$value) {
                if ($value instanceof IConfigValue) {
                    $value = $value->get();
                } elseif ($value instanceof IConfig) {
                    $value = $value->toArray();
                } else {
                    throw new UnexpectedValueException($this->translate(
                        'Config source is corrupted.'
                    ));
                }
            }
        );

        return $src;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $values)
    {
        $this->mergeArrayValues($this->source, $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return $this->hasPath(explode(self::PATH_SEPARATOR, $path), $this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function del($path)
    {
        $this->deleteByPath(
            explode(self::PATH_SEPARATOR, $path),
            $this->source
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset($path = null)
    {
        $keys = $path ? explode(self::PATH_SEPARATOR, $path) : [];
        $this->resetByPath($keys, $this->source);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return ['source'];
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = current($this->source);

        if ($current instanceof IConfigValue) {
            return $current->get();
        } elseif (is_array($current)) {
            return new self($current); // todo: is it hack or feature?
        } else {
            return $current;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return key($this->source) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->source);
    }

    /**
     * Создает значение конфигурации.
     * @param string $value
     * @return IConfigValue
     */
    protected function createConfigValue($value)
    {
        return (new ConfigValue())->set($value);
    }

    /**
     * Устанавливает значение по заданной цепочке ключей.
     * @param array $keys цепочка ключей
     * @param mixed $value значение
     * @param array $source контейнер
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    private function setByPath(array $keys, $value, array &$source)
    {
        $key = array_shift($keys);

        if (!isset($source[$key])) {
            if ($keys) {
                $source[$key] = [];
                $this->setByPath($keys, $value, $source[$key]);
            } else {
                $source[$key] = $this->createConfigValue($value);
            }

            return;
        }

        $srcValue =& $source[$key];

        if (!$keys) {
            if ($srcValue instanceof IConfig) {
                if (!is_array($value)) {
                    throw new InvalidArgumentException($this->translate(
                        'Value should be an array, "{type}" has been given',
                        ['type' => gettype($value)]
                    ));
                }

                $srcValue->merge($value);
            } elseif (is_array($srcValue)) {
                if (!is_array($value)) {
                    throw new InvalidArgumentException($this->translate(
                        'Value should be an array, "{type}" has been given',
                        ['type' => gettype($value)]
                    ));
                }

                $this->mergeArrayValues($srcValue, $value);
            } elseif ($srcValue instanceof IConfigValue) {
                $srcValue->set($value, IConfigValue::KEY_AUTO);
            }
        } else {
            if ($srcValue instanceof IConfig) {
                $srcValue->set(implode(self::PATH_SEPARATOR, $keys), $value);
            } elseif (is_array($srcValue)) {
                $this->setByPath($keys, $value, $srcValue);
            } else {
                throw new InvalidArgumentException($this->translate(
                    "Trying to set sub key for scalar value."
                ));
            }
        }
    }

    /**
     * Ищет значение по заданной цепочке ключей.
     * @param array $keys цепочка ключей
     * @param array $source контейнер
     * @throws UnexpectedValueException при попытке получения битого значения конфигурации
     * @throws InvalidArgumentException при попытке поиска внутри скалярного значения
     * @return null|mixed|IConfig
     */
    private function getByPath(array $keys, array &$source)
    {
        $key = array_shift($keys);

        if (!isset($source[$key])) {
            return null;
        }

        $value = & $source[$key];

        if (!$keys) {
            if ($value instanceof IConfig) {
                return $value;
            } elseif (is_array($value)) {
                return new self($value); // todo: is it hack or feature?
            } elseif ($value instanceof IConfigValue) {
                return $value->get();
            } else {
                throw new UnexpectedValueException($this->translate(
                    'Config source is corrupted.'
                ));
            }
        } else {
            if ($value instanceof IConfig) {
                return $value->get(implode(self::PATH_SEPARATOR, $keys));
            } elseif (is_array($value)) {
                return $this->getByPath($keys, $value);
            } else {
                throw new InvalidArgumentException($this->translate(
                    "Trying to get sub key for scalar value."
                ));
            }
        }

    }

    /**
     * Проверяет значение по заданной цепочке ключей.
     * @param array $keys цепочка ключей
     * @param array $source контейнер
     * @return bool
     */
    private function hasPath(array $keys, array &$source)
    {
        $key = array_shift($keys);

        if (!isset($source[$key])) {
            return false;
        }

        if (!$keys) {
            return true;
        } else {
            $value = & $source[$key];

            if ($value instanceof IConfig) {
                return $value->has(implode(self::PATH_SEPARATOR, $keys));
            } elseif (is_array($value)) {
                return $this->hasPath($keys, $value);
            } else {
                return false;
            }
        }
    }

    /**
     * Сбрасывает значение в цепочки ключей.
     * @param array $keys цепочка ключей
     * @param array $source контейнер
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function resetByPath(array $keys, array &$source)
    {
        if ($keys) {
            $key = array_shift($keys);

            if (!isset($source[$key])) {
                return;
            }

            $value = & $source[$key];
        } else {
            $value = & $source;
        }

        if (!$keys) {
            if (is_array($value)) {
                $input = & $value;
            } else {
                $input = [&$value];
            }

            array_walk_recursive(
                $input,
                function (&$val) {
                    if ($val instanceof IConfigValue) {
                        $val->reset();
                    } elseif ($val instanceof IConfig) {
                        $val->reset();
                    } else {
                        throw new UnexpectedValueException($this->translate(
                            'Config source is corrupted.'
                        ));
                    }
                }
            );
        } else {
            if ($value instanceof IConfig) {
                $value->reset(implode(self::PATH_SEPARATOR, $keys));
            } elseif (is_array($value)) {
                $this->resetByPath($keys, $value);
            } else {
                throw new InvalidArgumentException($this->translate(
                    "Trying to reset sub key for scalar value."
                ));
            }
        }
    }

    /**
     * Удаляет значение по заданной цепочке ключей.
     * @param array $keys цепочка ключей
     * @param array $source контейнер
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    private function deleteByPath(array $keys, array &$source)
    {
        $key = array_shift($keys);

        if (!isset($source[$key])) {
            return;
        }

        $value = & $source[$key];

        if (!$keys) {
            if (($value instanceof IConfig) || is_array($value)) {
                // todo: is it right?
                throw new UnexpectedValueException($this->translate(
                    'Cannot delete array of values.'
                ));
            } elseif ($value instanceof IConfigValue) {
                $value->del();
            } else {
                throw new UnexpectedValueException($this->translate(
                    'Config source is corrupted.'
                ));
            }
        } else {
            if ($value instanceof IConfig) {
                $value->del(implode(self::PATH_SEPARATOR, $keys));
            } elseif (is_array($value)) {
                $this->deleteByPath($keys, $value);
            } else {
                throw new InvalidArgumentException($this->translate(
                    "Trying to delete sub key for scalar value."
                ));
            }
        }
    }

    /**
     * Выполняет слияние массива значений конфига и массива скалярных значений.
     * @param array $source значения конфигурации
     * @param array $values
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    private function mergeArrayValues(array &$source, array &$values)
    {
        foreach ($values as $key => $value) {
            if (isset($source[$key])) {
                $sourceValue = $source[$key];

                if ($sourceValue instanceof IConfigValue) {
                    $sourceValue->set($value);
                } elseif (is_array($sourceValue) || $sourceValue instanceof IConfig) {
                    if (!is_array($value)) {
                        throw new InvalidArgumentException($this->translate(
                            'Value should be an array, "{type}" has been given',
                            ['type' => gettype($value)]
                        ));
                    }

                    if ($sourceValue instanceof IConfig) {
                        $sourceValue->merge($value);
                    } else {
                        $this->mergeArrayValues($sourceValue, $value);
                    }
                } else {
                    throw new UnexpectedValueException($this->translate(
                        'Config source is corrupted.'
                    ));
                }
            } else {
                if (is_array($value)) {
                    $source[$key] = [];
                    $this->mergeArrayValues($source[$key], $value);
                } else {
                    $source[$key] = $this->createConfigValue($value);
                }
            }
        }
    }

}
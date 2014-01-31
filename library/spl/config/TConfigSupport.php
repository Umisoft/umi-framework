<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\spl\config;

use InvalidArgumentException;
use Traversable;
use UnexpectedValueException;

/**
 * Трейт для поддержки конфигураций
 */
trait TConfigSupport
{

    /**
     * Преобразует конфиурацию к массиву.
     * @param mixed $config конфигурация
     * @param bool $fullDepth если true, то преобразование будет рекурсивное, на всю глубину конфигурации
     * @throws InvalidArgumentException если не удалось преобразовать конфигурацию к массиву
     * @return array
     */
    protected function configToArray($config, $fullDepth = false)
    {
        if ($config instanceof Traversable) {
            $config = iterator_to_array($config, true);
        }

        if (!is_array($config)) {
            throw new InvalidArgumentException('Cannot convert configuration to array.');
        }

        if ($fullDepth) {
            foreach ($config as $key => $value) {
                if (!is_scalar($value)) {
                    $config[$key] = $this->configToArray($value, $fullDepth);
                }
            }
        }

        return $config;
    }

    /**
     * Производит рекурсивное слияние указанных опций с опциями по умолчанию.
     * @param array $options массив опций
     * @param array $default массив опций по умолчанию
     * @throws UnexpectedValueException если какая-либо из опций по-умолчанию и желаемая опция различного типа
     * @return array
     */
    protected function mergeConfigOptions(array $options, array $default)
    {
        $result = [];

        foreach ($options as $name => $value) {
            if (isset($default[$name]) && $value !== $default[$name]) {
                $defaultValue = $default[$name];
                if ($defaultValue instanceof Traversable) {
                    $defaultValue = $this->configToArray($defaultValue);
                }

                if ($value instanceof Traversable) {
                    $value = $this->configToArray($value);
                }

                if (!is_null($defaultValue) && (gettype($defaultValue) !== gettype($value))) {
                    throw new UnexpectedValueException(sprintf(
                        'Cannot resolve option "%s". Option value should be of type %s.',
                        $name,
                        gettype($defaultValue)
                    ));
                }

                if (is_array($defaultValue)) {
                    $value = $this->configToArray($value);
                    $value = $this->mergeConfigOptions($value, $defaultValue);
                    $result[$name] = array_merge($defaultValue, $value);
                } else {
                    $result[$name] = $value;
                }
            } else {
                $result[$name] = $value;
            }
        }

        foreach ($default as $name => $value) {
            if (!isset($options[$name])) {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * Возвращает обязательную опцию конфигурации. Вызывает $failureCallback, если опция не найдена, либо не проходит валидацию.
     * @param array $options массив опций, в котором производится поиск
     * @param string $optionName имя опции
     * @param callable $failureCallback если указан, может выкинуть исключение, либо вернуть значение для опции по умолчанию
     * @param callable $validatorCallback анонимная функция-валидатор, которая будет вызвана для валидации значения опции.
     * Принимает на вход значение опции, должна вернуть false, в случае не валидного значения.
     * @return mixed
     */
    protected function getRequiredOption(
        array $options,
        $optionName,
        callable $failureCallback = null,
        callable $validatorCallback = null
    )
    {
        if (!array_key_exists($optionName, $options) ||
            ($validatorCallback && call_user_func($validatorCallback, $options[$optionName]) === false)
        ) {
            return $failureCallback ? call_user_func($failureCallback) : null;
        }

        return $options[$optionName];
    }

    /**
     * Возвращает валидатор опций, которые не должны быть empty
     * @return callable
     */
    protected function getEmptyOptionValidator()
    {
        static $validator;
        if (!$validator) {
            $validator = function ($value) {
                return empty($value);
            };
        }

        return $validator;
    }
}
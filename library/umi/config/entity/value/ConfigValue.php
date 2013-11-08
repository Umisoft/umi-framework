<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity\value;

use umi\config\exception\InvalidArgumentException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Значение конфигурации
 */
class ConfigValue implements IConfigValue, ILocalizable
{

    use TLocalizable;

    /**
     * @var array $values значения
     */
    protected $values = [];
    /**
     * @var array $modifiedValues значения
     */
    protected $modifiedValues = [];

    /**
     * Конструктор.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        ksort($values);

        $this->values = $values;
        $this->modifiedValues = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function get($type = self::KEY_AUTO)
    {
        if ($type === self::KEY_AUTO) {
            return $this->modifiedValues ? reset($this->modifiedValues) : null;
        } else {
            return isset($this->modifiedValues[$type]) ? $this->modifiedValues[$type] : null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($value, $type = self::KEY_AUTO)
    {
        if (!is_scalar($value) && !is_null($value)) {
            throw new InvalidArgumentException($this->translate(
                'Value should be a scalar, "{type}" has been given.',
                ['type' => gettype($value)]
            ));
        }
        $type = $type ? : self::KEY_LOCAL;

        $this->modifiedValues[$type] = $value;
        ksort($this->modifiedValues);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($type = self::KEY_AUTO)
    {
        if ($type === self::KEY_AUTO) {
            return !empty($this->modifiedValues);
        } else {
            return isset($this->modifiedValues[$type]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function del($type = self::KEY_AUTO)
    {
        $type = $type ? : self::KEY_LOCAL;

        unset($this->modifiedValues[$type]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->modifiedValues = $this->values;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->values = $this->modifiedValues;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return ['values'];
    }

    public function __wakeup()
    {
        $this->modifiedValues = $this->values;
    }
}
<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

use umi\spl\container\TArrayAccess;

/**
 * Лениво загружаемая конфигурация. Загрузка будет осуществлена в момент первого обращения.
 */
abstract class BaseSeparateConfigSource implements ISeparateConfigSource
{

    use TArrayAccess;

    /**
     * {@inheritdoc}
     */
    public function get($path)
    {
        return $this->getSeparateConfig()
            ->get($path);
    }

    /**
     * {@inheritdoc}
     */
    public function set($path, $value)
    {
        $this->getSeparateConfig()
            ->set($path, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return $this->getSeparateConfig()
            ->has($path);
    }

    /**
     * {@inheritdoc}
     */
    public function del($path)
    {
        $this->getSeparateConfig()
            ->del($path);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->getSeparateConfig()
            ->getSource();
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $array)
    {
        $this->getSeparateConfig()
            ->merge($array);
    }

    /**
     * {@inheritdoc}
     */
    public function reset($path = null)
    {
        $this->getSeparateConfig()
            ->reset($path);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->getSeparateConfig());
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->getSeparateConfig()
            ->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->getSeparateConfig()
            ->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->getSeparateConfig()
            ->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->getSeparateConfig()
            ->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->getSeparateConfig()
            ->rewind();
    }
}
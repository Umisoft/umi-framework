<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

/**
 * Конфигурация.
 */
class ConfigSource extends Config implements IConfigSource
{
    /**
     * @var string $alias символическое имя
     */
    protected $alias;

    /**
     * Конструктор.
     * @param array $source данные конфигурации
     * @param string $alias символическое имя
     */
    public function __construct(array &$source, $alias)
    {
        $this->alias = $alias;

        parent::__construct($source);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['alias']);
    }
}
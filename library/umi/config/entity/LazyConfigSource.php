<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

use umi\config\io\IConfigIOAware;
use umi\config\io\TConfigIOAware;

/**
 * Лениво загружаемая конфигурация. Загрузка будет осуществлена в момент первого обращения.
 */
class LazyConfigSource extends BaseSeparateConfigSource implements IConfigIOAware
{

    use TConfigIOAware;

    /**
     * @var string $alias
     */
    protected $alias;
    /**
     * @var IConfigSource $source
     */
    protected $source;

    /**
     * {@inheritdoc}
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
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
    public function toArray()
    {
        return $this->getSeparateConfig()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparateConfig()
    {
        if (!$this->source) {
            $this->source = $this->readConfig($this->alias);
        }

        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return ['alias'];
    }
}
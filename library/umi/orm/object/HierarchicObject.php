<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object;

use umi\orm\collection\IHierarchicCollection;

/**
 * Иерархический объект.
 */
class HierarchicObject extends Object implements IHierarchicObject
{
    /**
     * @var IHierarchicCollection $collection коллекция, к которой принадлежит объект
     */
    protected $collection;
    /**
     * @var string $normalizedURL нормализованный url объекта
     */
    private $normalizedURL;

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getProperty(self::FIELD_PARENT)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getMaterializedPath()
    {
        return $this->getProperty(self::FIELD_MPATH)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->getProperty(self::FIELD_ORDER)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->getProperty(self::FIELD_HIERARCHY_LEVEL)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getChildCount()
    {
        return $this->getProperty(self::FIELD_CHILD_COUNT)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getURI()
    {
        return $this->getProperty(self::FIELD_URI)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getURL()
    {
        if (is_null($this->normalizedURL)) {
            $url = $this->getProperty(self::FIELD_URI)
                ->getValue();
            $this->normalizedURL = substr($url, 1);
        }

        return $this->normalizedURL;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->getProperty(self::FIELD_SLUG)
            ->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        parent::reset();
        $this->normalizedURL = null;
    }

}

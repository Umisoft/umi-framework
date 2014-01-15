<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;

/**
 * Коллекция связанных иерархических объектов.
 * Особоый вид иерархической коллекции, которая связана с общей иерархией (ICommonHierarchy).
 */
class LinkedHierarchicCollection extends SimpleHierarchicCollection implements ILinkedHierarchicCollection
{

    /**
     * @var ICommonHierarchy $commonHierarchy общая иерархия
     */
    protected $commonHierarchy;

    /**
     * {@inheritdoc}
     */
    public function setCommonHierarchy(ICommonHierarchy $hierarchy)
    {
        if (!$this->commonHierarchy) {
            $this->commonHierarchy = $hierarchy;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommonHierarchy()
    {
        if (!$this->commonHierarchy) {
            throw new RuntimeException($this->translate(
                'Common hierarchy for linked collection {name} are not injected.',
                ['name' => $this->name]
            ));
        }

        return $this->commonHierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist new object. Object from another collection given.'
            ));
        }
        $this->getCommonHierarchy()
            ->persistNewObject($object);
        parent::persistNewObject($object);
    }

    /**
     * {@inheritdoc}
     */
    public function persistModifiedObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist modified object. Object from another collection given.'
            ));
        }
        $this->getCommonHierarchy()
            ->persistModifiedObject($object);
        parent::persistModifiedObject($object);
    }

    /**
     * {@inheritdoc}
     */
    public function persistDeletedObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist deleted object. Object from another collection given.'
            ));
        }
        $this->getCommonHierarchy()
            ->persistDeletedObject($object);
        parent::persistDeletedObject($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxOrder(IHierarchicObject $branch = null)
    {
        return $this->getCommonHierarchy()
            ->getMaxOrder($branch);
    }

    /**
     * {@inheritdoc}
     */
    public function move(
        IHierarchicObject $object,
        IHierarchicObject $branch = null,
        IHierarchicObject $previousSibling = null
    )
    {
        $this->getCommonHierarchy()
            ->move($object, $branch, $previousSibling);
    }

    /**
     * {@inheritdoc}
     */
    public function changeSlug(IHierarchicObject $object, $slug)
    {
        $this->getCommonHierarchy()
            ->changeSlug($object, $slug);
    }

    /**
     * {@inheritdoc}
     */
    public function selectAncestry(IHierarchicObject $object)
    {
        return $this->getCommonHierarchy()
            ->selectAncestry($object);
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\object\IObject;
use umi\orm\objectset\IObjectSetFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика наборов объектов.
 */
class ObjectSetFactory implements IObjectSetFactory, IFactory
{

    use TFactory;

    /**
     * @var string $objectsSetClass имя класса для создания набора объектов
     */
    public $objectSetClass = 'umi\orm\objectset\ObjectSet';
    /**
     * @var string $objectsSetClass имя класса для создания пустого набора объектов
     */
    public $emptyObjectSetClass = 'umi\orm\objectset\EmptyObjectSet';
    /**
     * @var string $manyToManyObjectSetClass имя класса для создания набора объектов,
     * представляющего связь многие-ко-многим
     */
    public $manyToManyObjectSetClass = 'umi\orm\objectset\ManyToManyObjectSet';

    /**
     * {@inheritdoc}
     */
    public function createObjectSet()
    {
        return $this->getPrototype(
                $this->objectSetClass,
                ['umi\orm\objectset\IObjectSet']
            )
            ->createInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function createEmptyObjectSet()
    {
        return $this->getPrototype(
                $this->emptyObjectSetClass,
                ['umi\orm\objectset\IEmptyObjectSet']
            )
            ->createInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function createManyToManyObjectSet(IObject $object, ManyToManyRelationField $manyToManyRelationField)
    {
        return $this->getPrototype(
                $this->manyToManyObjectSetClass,
                ['umi\orm\objectset\IManyToManyObjectSet']
            )
            ->createInstance([$object, $manyToManyRelationField]);
    }
}

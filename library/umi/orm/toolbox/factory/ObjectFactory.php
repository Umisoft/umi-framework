<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\orm\collection\ICollection;
use umi\orm\collection\ICommonHierarchy;
use umi\orm\collection\IHierarchicCollection;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;
use umi\orm\object\IObjectFactory;
use umi\orm\object\property\IPropertyFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика объектов.
 */
class ObjectFactory implements IObjectFactory, IFactory
{

    use TFactory;

    /**
     * @var string $defaultObjectClass имя класса для объекта по умолчанию
     */
    public $defaultObjectClass = 'umi\orm\object\Object';
    /**
     * @var string $defaultHierarchicObjectClass имя класса для иерархического объекта по умолчанию
     */
    public $defaultHierarchicObjectClass = 'umi\orm\object\HierarchicObject';

    /**
     * @var IPropertyFactory $propertyFactory фабрика свойств объекта
     */
    protected $propertyFactory;

    /**
     * Конструктор.
     * @param IPropertyFactory $propertyFactory фабрика свойств объекта
     */
    public function __construct(IPropertyFactory $propertyFactory)
    {
        $this->propertyFactory = $propertyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createObject(ICollection $collection, IObjectType $objectType)
    {
        return $this->getPrototype(
                $this->getObjectClass($collection, $objectType),
                [$this->getObjectContract($collection)]
            )
            ->createInstance([$collection, $objectType, $this->propertyFactory]);
    }

    /**
     * {@inheritdoc}
     */
    public function wakeUpObject(IObject $object, ICollection $collection, IObjectType $objectType)
    {
        $prototype = $this->getPrototype(get_class($object));
        $prototype->wakeUpInstance($object);
        $prototype->invokeConstructor($object, [$collection, $objectType, $this->propertyFactory]);
    }

    /**
     * Возвращает класс для объекта коллекции.
     * @param ICollection $collection коллекция
     * @return string
     */
    private function getObjectContract(ICollection $collection)
    {
        if ($collection instanceof IHierarchicCollection || $collection instanceof ICommonHierarchy) {
            return 'umi\orm\object\IHierarchicObject';
        } else {
            return 'umi\orm\object\IObject';
        }
    }

    /**
     * Возвращает класс для объекта коллекции.
     * @param ICollection $collection коллекция
     * @param IObjectType $objectType тип объекта
     * @return string
     */
    private function getObjectClass(ICollection $collection, IObjectType $objectType)
    {
        if (null != ($objectClass = $objectType->getObjectClass())) {
            return $objectClass;
        } elseif ($collection instanceof IHierarchicCollection || $collection instanceof ICommonHierarchy) {
            return $this->defaultHierarchicObjectClass;
        } else {
            return $this->defaultObjectClass;
        }
    }
}

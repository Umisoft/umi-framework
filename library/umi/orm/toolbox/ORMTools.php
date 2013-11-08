<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox;

use umi\dbal\cluster\IDbCluster;
use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\metadata\IMetadataFactory;
use umi\orm\metadata\IMetadataManagerAware;
use umi\orm\object\IObjectFactory;
use umi\orm\objectset\IObjectSetFactory;
use umi\orm\persister\IObjectPersisterAware;
use umi\orm\selector\ISelectorFactory;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструментарий ORM.
 */
class ORMTools implements IORMTools
{

    use TToolbox;

    /**
     * @var string $collectionFactoryClass класс для создания фабрики коллекции объектов
     */
    public $collectionFactoryClass = 'umi\orm\toolbox\factory\CollectionFactory';
    /**
     * @var string $collectionManagerClass класс для создания менеджера коллекций
     */
    public $collectionManagerClass = 'umi\orm\collection\CollectionManager';
    /**
     * @var string $selectorFactoryClass класс для создания фабрики селекторов
     */
    public $selectorFactoryClass = 'umi\orm\toolbox\factory\SelectorFactory';
    /**
     * @var string $objectLoaderClass класс для создания загрузчика объектов
     */
    public $objectLoaderClass = 'umi\orm\loader\ObjectLoader';
    /**
     * @var string $metadataFactoryClass класс для создания фабрики метаданных
     */
    public $metadataFactoryClass = 'umi\orm\toolbox\factory\MetadataFactory';
    /**
     * @var string $metadataManagerClass класс для создания менеджера метаданных
     */
    public $metadataManagerClass = 'umi\orm\metadata\MetadataManager';
    /**
     * @var string $objectFactoryClass класс для создания фабрики объектов
     */
    public $objectFactoryClass = 'umi\orm\toolbox\factory\ObjectFactory';
    /**
     * @var string $propertyFactoryClass класс для создания фабрики свойств объекта
     */
    public $propertyFactoryClass = 'umi\orm\toolbox\factory\PropertyFactory';
    /**
     * @var string $objectSetFactoryClass класс для создания фабрики наборов объектов
     */
    public $objectSetFactoryClass = 'umi\orm\toolbox\factory\ObjectSetFactory';
    /**
     * @var string $objectManagerClass класс для создания менеджера объектов
     */
    public $objectManagerClass = 'umi\orm\manager\ObjectManager';
    /**
     * @var string $objectPersisterClass класс для создания синхронизатора объектов
     */
    public $objectPersisterClass = 'umi\orm\persister\ObjectPersister';
    /**
     * @var array $collectionManager опции менеджера коллекций
     */
    public $collectionManager = [];
    /**
     * @var array $metadataManager опции менеджера метаданных
     */
    public $metadataManager = [];

    /**
     * @var IDbCluster $dbCluster сервис для работы c БД
     */
    protected $dbCluster;

    /**
     * Конструктор
     */
    public function __construct(IDbCluster $dbCluster)
    {
        $this->dbCluster = $dbCluster;

        $this->registerFactory(
            'objectCollectionFactory',
            $this->collectionFactoryClass,
            ['umi\orm\collection\ICollectionFactory']
        );
        $this->registerFactory(
            'selectorFactory',
            $this->selectorFactoryClass,
            ['umi\orm\selector\ISelectorFactory']
        );
        $this->registerFactory(
            'metadataFactory',
            $this->metadataFactoryClass,
            ['umi\orm\metadata\IMetadataFactory']
        );
        $this->registerFactory('objectFactory', $this->objectFactoryClass, ['umi\orm\object\IObjectFactory']);
        $this->registerFactory(
            'propertyFactory',
            $this->propertyFactoryClass,
            ['umi\orm\object\property\IPropertyFactory']
        );
        $this->registerFactory(
            'objectSetFactory',
            $this->objectSetFactoryClass,
            ['umi\orm\objectset\IObjectSetFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IObjectManagerAware) {
            $object->setObjectManager($this->getObjectManager());
        }
        if ($object instanceof IObjectPersisterAware) {
            $object->setObjectPersister($this->getObjectPersister());
        }
        if ($object instanceof ICollectionManagerAware) {
            $object->setCollectionManager($this->getCollectionManager());
        }
        if ($object instanceof IMetadataManagerAware) {
            $object->setMetadataManager($this->getMetadataManager());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectManager()
    {
        if (null != ($instance = $this->getSingleInstance($this->objectManagerClass))) {
            return $instance;
        }

        return $this->createSingleInstance(
            $this->objectManagerClass,
            [$this->getObjectFactory()],
            ['umi\orm\manager\IObjectManager']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataManager()
    {
        if (null != ($instance = $this->getSingleInstance($this->metadataManagerClass))) {
            return $instance;
        }

        return $this->createSingleInstance(
            $this->metadataManagerClass,
            [$this->getMetadataFactory()],
            ['umi\orm\metadata\IMetadataManager'],
            $this->metadataManager
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionManager()
    {
        if (null != ($instance = $this->getSingleInstance($this->collectionManagerClass))) {
            return $instance;
        }

        return $this->createSingleInstance(
            $this->collectionManagerClass,
            [$this->getObjectCollectionFactory()],
            ['umi\orm\collection\ICollectionManager'],
            $this->collectionManager
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectPersister()
    {
        return $this->createSingleInstance(
            $this->objectPersisterClass,
            [],
            ['umi\orm\persister\IObjectPersister']
        );
    }

    /**
     * Создает и возвращает фабрику коллекций объектов.
     * @return ICollectionFactory
     */
    protected function getObjectCollectionFactory()
    {
        return $this->getFactory('objectCollectionFactory', [$this->getSelectorFactory()]);
    }

    /**
     * Создает и возвращает фабрику cелекторов.
     * @return ISelectorFactory
     */
    protected function getSelectorFactory()
    {
        return $this->getFactory('selectorFactory', [$this->getObjectSetFactory()]);
    }

    /**
     * Создает и возвращает фабрику метаданных.
     * @return IMetadataFactory
     */
    protected function getMetadataFactory()
    {
        return $this->getFactory('metadataFactory', [$this->dbCluster]);
    }

    /**
     * Создает и возвращает фабрику объектов.
     * @return IObjectFactory
     */
    protected function getObjectFactory()
    {
        return $this->getFactory('objectFactory', [$this->getPropertyFactory()]);
    }

    /**
     * Создает и возвращает фабрику свойств объекта.
     * @return IObjectFactory
     */
    protected function getPropertyFactory()
    {
        return $this->getFactory('propertyFactory');
    }

    /**
     * Создает и возвращает фабрику наборов объектов.
     * @return IObjectSetFactory
     */
    protected function getObjectSetFactory()
    {
        return $this->getFactory('objectSetFactory');
    }
}

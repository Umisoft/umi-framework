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
use umi\orm\collection\ICollectionManager;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\manager\IObjectManager;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\metadata\IMetadataFactory;
use umi\orm\metadata\IMetadataManager;
use umi\orm\metadata\IMetadataManagerAware;
use umi\orm\object\IObjectFactory;
use umi\orm\objectset\IObjectSetFactory;
use umi\orm\persister\IObjectPersister;
use umi\orm\persister\IObjectPersisterAware;
use umi\orm\selector\ISelectorFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструментарий ORM.
 */
class ORMTools implements IToolbox
{
    /** Имя набора инструментов */
    const NAME = 'orm';

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
     * @var array $collections конфигурация коллекций для менеджера коллекций
     */
    public $collections = [];
    /**
     * @var array $metadata конфигурация метаданных коллекций для менеджера метаданных
     */
    public $metadata = [];

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
            'objectCollection',
            $this->collectionFactoryClass,
            ['umi\orm\collection\ICollectionFactory']
        );
        $this->registerFactory(
            'selector',
            $this->selectorFactoryClass,
            ['umi\orm\selector\ISelectorFactory']
        );
        $this->registerFactory(
            'metadata',
            $this->metadataFactoryClass,
            ['umi\orm\metadata\IMetadataFactory']
        );
        $this->registerFactory('objectFactory', $this->objectFactoryClass, ['umi\orm\object\IObjectFactory']);
        $this->registerFactory(
            'property',
            $this->propertyFactoryClass,
            ['umi\orm\object\property\IPropertyFactory']
        );
        $this->registerFactory(
            'objectSet',
            $this->objectSetFactoryClass,
            ['umi\orm\objectset\IObjectSetFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\orm\collection\ICollectionManager':
                return $this->getCollectionManager();
            case 'umi\orm\persister\IObjectPersister':
                return $this->getObjectPersister();
            case 'umi\orm\manager\IObjectManager':
                return $this->getObjectManager();
            case 'umi\orm\metadata\IMetadataManager':
                return $this->getMetadataManager();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
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
     * Возвращает менеджер объектов
     * @return IObjectManager
     */
    public function getObjectManager()
    {
        return $this->getPrototype(
            $this->objectManagerClass,
            ['umi\orm\manager\IObjectManager']
        )
            ->createSingleInstance([$this->getObjectFactory()]);
    }

    /**
     * Возвращает менеджер метаданных
     * @return IMetadataManager
     */
    protected function getMetadataManager()
    {

        return $this->getPrototype(
            $this->metadataManagerClass,
            ['umi\orm\metadata\IMetadataManager']
        )
            ->createSingleInstance(
                [
                    $this->getMetadataFactory(),
                    $this->metadata
                ]
            );
    }

    /**
     * Возвращает менеджер метаданных
     * @return ICollectionManager
     */
    protected function getCollectionManager()
    {
        return $this->getPrototype(
            $this->collectionManagerClass,
            ['umi\orm\collection\ICollectionManager']
        )
            ->createSingleInstance(
                [
                    $this->getObjectCollectionFactory(),
                    $this->collections
                ]
            );
    }

    /**
     * Возвращает синхронизатор объектов
     * @return IObjectPersister
     */
    protected function getObjectPersister()
    {
        return $this
            ->getPrototype($this->objectPersisterClass, ['umi\orm\persister\IObjectPersister'])
            ->createSingleInstance();
    }

    /**
     * Создает и возвращает фабрику коллекций объектов.
     * @return ICollectionFactory
     */
    protected function getObjectCollectionFactory()
    {
        return $this->getFactory('objectCollection', [$this->getSelectorFactory()]);
    }

    /**
     * Создает и возвращает фабрику cелекторов.
     * @return ISelectorFactory
     */
    protected function getSelectorFactory()
    {
        return $this->getFactory('selector', [$this->getObjectSetFactory()]);
    }

    /**
     * Создает и возвращает фабрику метаданных.
     * @return IMetadataFactory
     */
    protected function getMetadataFactory()
    {
        return $this->getFactory('metadata', [$this->dbCluster]);
    }

    /**
     * Создает и возвращает фабрику объектов.
     * @return IObjectFactory
     */
    protected function getObjectFactory()
    {
        return $this->getFactory('object', [$this->getPropertyFactory()]);
    }

    /**
     * Создает и возвращает фабрику свойств объекта.
     * @return IObjectFactory
     */
    protected function getPropertyFactory()
    {
        return $this->getFactory('property');
    }

    /**
     * Создает и возвращает фабрику наборов объектов.
     * @return IObjectSetFactory
     */
    protected function getObjectSetFactory()
    {
        return $this->getFactory('objectSet');
    }
}

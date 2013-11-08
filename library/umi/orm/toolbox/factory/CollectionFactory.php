<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\ICommonHierarchy;
use umi\orm\collection\IHierarchicCollection;
use umi\orm\collection\ILinkedHierarchicCollection;
use umi\orm\collection\ISimpleCollection;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\RuntimeException;
use umi\orm\metadata\IMetadata;
use umi\orm\selector\ISelectorFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика коллекий объектов.
 */
class CollectionFactory implements ICollectionFactory, IFactory, ICollectionManagerAware
{

    use TFactory;
    use TCollectionManagerAware;

    /**
     * @var  $selectorFactory
     */
    protected $selectorFactory;
    /**
     * @var string $defaultSimpleCollectionClass имя класса простой коллекции по умолчанию
     */
    public $defaultSimpleCollectionClass = 'umi\orm\collection\SimpleCollection';
    /**
     * @var string $defaultHierarchicCollectionClass имя класса иерархической коллекции по умолчанию
     */
    public $defaultHierarchicCollectionClass = 'umi\orm\collection\SimpleHierarchicCollection';
    /**
     * @var string $defaultLinkedHierarchicCollectionClass имя класса связанной иерархической коллекции по умолчанию
     */
    public $defaultLinkedHierarchicCollectionClass = 'umi\orm\collection\LinkedHierarchicCollection';
    /**
     * @var string $defaultCommonHierarchyClass имя класса общей иерархии по умолчанию
     */
    public $defaultCommonHierarchyClass = 'umi\orm\collection\CommonHierarchy';

    /**
     * Конструктор
     * @param ISelectorFactory $selectorFactory фабрика селекторов
     */
    public function __construct(ISelectorFactory $selectorFactory)
    {
        $this->selectorFactory = $selectorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($collectionName, IMetadata $metadata, array $config)
    {
        if (!isset($config['type'])) {
            throw new RuntimeException($this->translate(
                'Collection configuration should contain type info.'
            ));
        }
        switch ($config['type']) {
            case self::TYPE_SIMPLE:
                return $this->createSimpleCollection($collectionName, $metadata, $config);
            case self::TYPE_SIMPLE_HIERARCHIC:
                return $this->createSimpleHierarchicCollection($collectionName, $metadata, $config);
            case self::TYPE_LINKED_HIERARCHIC:
                return $this->createLinkedHierarchicCollection($collectionName, $metadata, $config);
            case self::TYPE_COMMON_HIERARCHY:
                return $this->createCommonHierarchy($collectionName, $metadata, $config);
            default:
                throw new RuntimeException($this->translate(
                    'Cannot create collection. Unknown collection type "{type}".',
                    ['type' => $config['type']]
                ));
        }
    }

    /**
     * Создает экземпляр простой коллекции объектов
     * @param string $collectionName имя коллекции
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции
     * @return ISimpleCollection
     */
    protected function createSimpleCollection($collectionName, IMetadata $metadata, array $config)
    {
        if (!isset($config['class'])) {
            $config['class'] = $this->defaultSimpleCollectionClass;
        }

        return $this->createInstance(
            $config['class'],
            [$collectionName, $metadata, $this->selectorFactory],
            ['umi\orm\collection\ISimpleCollection']
        );
    }

    /**
     * Создает экземпляр иерархической коллекции объектов
     * @param string $collectionName имя коллекции
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции
     * @return IHierarchicCollection
     */
    protected function createSimpleHierarchicCollection($collectionName, IMetadata $metadata, array $config)
    {
        if (!isset($config['class'])) {
            $config['class'] = $this->defaultHierarchicCollectionClass;
        }

        return $this->createInstance(
            $config['class'],
            [$collectionName, $metadata, $this->selectorFactory],
            ['umi\orm\collection\ISimpleHierarchicCollection']
        );
    }

    /**
     * Создает экземпляр связанной иерархической коллекции объектов
     * @param string $collectionName имя коллекции
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции
     * @throws RuntimeException если не удалось создать коллекцию
     * @return ILinkedHierarchicCollection
     */
    protected function createLinkedHierarchicCollection($collectionName, IMetadata $metadata, array $config)
    {
        if (!isset($config['hierarchy'])) {
            throw new RuntimeException($this->translate(
                'Cannot create linked collection. Configuration should contain "hierarchy" option.'
            ));
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->defaultLinkedHierarchicCollectionClass;
        }

        /**
         * @var ILinkedHierarchicCollection $linkedCollection
         */
        $linkedCollection = $this->createInstance(
            $config['class'],
            [$collectionName, $metadata, $this->selectorFactory],
            ['umi\orm\collection\ILinkedHierarchicCollection']
        );

        $commonHierarchy = $this->getCollectionManager()
            ->getCollection($config['hierarchy']);
        if (!$commonHierarchy instanceof ICommonHierarchy) {
            throw new RuntimeException($this->translate(
                'Cannot create linked collection. Relation collection should be instance of ICommonHierarchy.'
            ));
        }

        $linkedCollection->setCommonHierarchy($commonHierarchy);

        return $linkedCollection;
    }

    /**
     * Создает экземпляр общей иерархии
     * @param string $collectionName имя коллекции
     * @param IMetadata $metadata метаданные коллекции
     * @param array $config конфигурация коллекции
     * @return ICommonHierarchy
     */
    protected function createCommonHierarchy($collectionName, IMetadata $metadata, array $config)
    {
        if (!isset($config['class'])) {
            $config['class'] = $this->defaultCommonHierarchyClass;
        }

        return $this->createInstance(
            $config['class'],
            [$collectionName, $metadata, $this->selectorFactory],
            ['umi\orm\collection\ICommonHierarchy']
        );
    }
}

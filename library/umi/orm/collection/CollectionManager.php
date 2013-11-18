<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\UnexpectedValueException;
use umi\orm\metadata\IMetadataManagerAware;
use umi\orm\metadata\TMetadataManagerAware;
use umi\spl\config\TConfigSupport;

/**
 * Менеджер коллекций.
 */
class CollectionManager implements ICollectionManager, ILocalizable, IMetadataManagerAware
{

    use TLocalizable;
    use TMetadataManagerAware;
    use TConfigSupport;

    /**
     * @var array $collections список коллекций в формате
     * ['collectionName' => [
     *    'type' => 'simple',
     *    'class' => null | 'className',
     *    'hierarchy' => 'collectionName' // для linked коллекций
     *    ], ...
     * ]
     */
    protected $collections = [];
    /**
     * @var ICollectionFactory $objectCollectionFactory фабрика метаданных
     */
    protected $objectCollectionFactory;
    /**
     * @var ISimpleCollection[] $instances список созданных экземпляров коллекций: массив вида array(collectionName => ISimpleCollection, ...)
     */
    protected $instances = [];

    /**
     * Конструктор.
     * @param ICollectionFactory $objectCollectionFactory
     * @param array|\Traversable $collections конфигурация коллекций в формате
     * [
     *      'collectionName' => [
     *          'type' => 'simple',
     *          'class' => null | 'className',
     *          'hierarchy' => 'collectionName' // для linked коллекций
     *      ],
     *      ...
     * ]
     * @throws UnexpectedValueException в случае неверно заданной конфигурации коллекций
     */
    public function __construct(ICollectionFactory $objectCollectionFactory, $collections)
    {
        $this->objectCollectionFactory = $objectCollectionFactory;

        try {
            $collections = $this->configToArray($collections, true);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException($this->translate(
                'Invalid collections configuration.'
            ), 0, $e);
        }
        $this->collections = $collections;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCollection($collectionName)
    {
        return in_array($collectionName, $this->getList());
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($collectionName)
    {
        if (isset($this->instances[$collectionName])) {
            return $this->instances[$collectionName];
        }
        if (!$this->hasCollection($collectionName)) {
            throw new NonexistentEntityException($this->translate(
                'Object collection "{collection}" is not registered.',
                ["collection" => $collectionName]
            ));
        }

        $config = $this->collections[$collectionName];
        if ($config instanceof \Traversable) {
            $config = iterator_to_array($config);
        }
        if (!is_array($config)) {
            throw new UnexpectedValueException($this->translate(
                'Configuration for collection "{collection}" is not valid.',
                ['collection' => $collectionName]
            ));
        }

        $metadata = $this->getMetadataManager()
            ->getMetadata($collectionName);
        $collection = $this->objectCollectionFactory->create($collectionName, $metadata, $config);

        return $this->instances[$collectionName] = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return array_keys($this->collections);
    }

}

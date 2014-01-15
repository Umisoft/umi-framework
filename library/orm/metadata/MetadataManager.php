<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\UnexpectedValueException;
use umi\spl\config\TConfigSupport;

/**
 * Менеджер метаданных.
 */
class MetadataManager implements IMetadataManager, ILocalizable
{

    use TLocalizable;
    use TConfigSupport;

    /**
     * @var array $metadata конфигурация метаданных в формате
     * ['collectionName' => [], ... ]
     */
    protected $metadata = [];
    /**
     * @var IMetadataFactory $metadataFactory фабрика метаданных
     */
    protected $metadataFactory;
    /**
     * @var IMetadata[] $metadataInstances список созданных экземпляров metadata:
     * массив вида array(collectionName => IMetadata, ...)
     */
    protected $metadataInstances = [];

    /**
     * Конструктор.
     * @param IMetadataFactory $metadataFactory фабрика метаданных
     * @param array|\Traversable $metadata конфигурация метаданных в формате
     * [
     *      'collectionName' => [],
     *      ...
     * ]
     * @throws UnexpectedValueException в случае неверной конфигурации
     */
    public function __construct(IMetadataFactory $metadataFactory, $metadata)
    {
        $this->metadataFactory = $metadataFactory;
        try {
            $metadata = $this->configToArray($metadata);
        } catch (\InvalidArgumentException $e) {
            throw new UnexpectedValueException($this->translate(
                'Invalid collections configuration.'
            ), 0, $e);
        }
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($collectionName)
    {
        if (isset($this->metadataInstances[$collectionName])) {
            return $this->metadataInstances[$collectionName];
        }
        if (!$this->hasMetadata($collectionName)) {
            throw new NonexistentEntityException($this->translate(
                'Cannot get metadata. Metadata for collection "{collection}" does not exist.',
                ['collection' => $collectionName]
            ));
        }
        $metadata = $this->metadataFactory->create($collectionName, $this->metadata[$collectionName]);

        return $this->metadataInstances[$collectionName] = $metadata;
    }

    /**
     * Проверяет, зарегистрирована ли метаданные коллекции объектов
     * @param string $collectionName имя коллекции
     * @return boolean
     */
    protected function hasMetadata($collectionName)
    {
        return array_key_exists($collectionName, $this->metadata);
    }
}

<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\dbal\cluster\IDbCluster;
use umi\orm\exception\UnexpectedValueException;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IMetadataFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания метаданных.
 */
class MetadataFactory implements IMetadataFactory, IFactory
{

    use TFactory;

    /**
     * @var string $metadataClassName класс метаданных
     */
    public $metadataClassName = 'umi\orm\metadata\Metadata';
    /**
     * @var string $objectTypeClass имя класса типа объекта
     */
    public $objectTypeClass = 'umi\orm\metadata\ObjectType';
    /**
     * @var string $dataSourceClass имя класса для источника данных коллекции
     */
    public $dataSourceClass = 'umi\orm\metadata\CollectionDataSource';

    /**
     * @var array $fieldTypes поддерживаемые типы полей
     */
    public $fieldTypes = [
        // relation
        IField::TYPE_BELONGS_TO   => 'umi\orm\metadata\field\relation\BelongsToRelationField',
        IField::TYPE_MANY_TO_MANY => 'umi\orm\metadata\field\relation\ManyToManyRelationField',
        IField::TYPE_HAS_MANY     => 'umi\orm\metadata\field\relation\HasManyRelationField',
        IField::TYPE_HAS_ONE      => 'umi\orm\metadata\field\relation\HasOneRelationField',
        // numeric
        IField::TYPE_INTEGER      => 'umi\orm\metadata\field\numeric\IntegerField',
        IField::TYPE_DECIMAL      => 'umi\orm\metadata\field\numeric\DecimalField',
        IField::TYPE_REAL         => 'umi\orm\metadata\field\numeric\RealField',
        IField::TYPE_BOOL         => 'umi\orm\metadata\field\numeric\BoolField',
        // string's & blobs
        IField::TYPE_STRING       => 'umi\orm\metadata\field\string\StringField',
        IField::TYPE_CHAR         => 'umi\orm\metadata\field\string\CharField',
        IField::TYPE_TEXT         => 'umi\orm\metadata\field\string\TextField',
        IField::TYPE_BLOB         => 'umi\orm\metadata\field\string\BlobField',
        // date & time
        IField::TYPE_TIMESTAMP    => 'umi\orm\metadata\field\datetime\TimestampField',
        IField::TYPE_DATE         => 'umi\orm\metadata\field\datetime\DateField',
        IField::TYPE_TIME         => 'umi\orm\metadata\field\datetime\TimeField',
        IField::TYPE_DATE_TIME    => 'umi\orm\metadata\field\datetime\DateTimeField',
        // spacial fields
        IField::TYPE_IDENTIFY     => 'umi\orm\metadata\field\special\IdentifyField',
        IField::TYPE_GUID         => 'umi\orm\metadata\field\special\GuidField',
        IField::TYPE_VERSION      => 'umi\orm\metadata\field\special\VersionField',
        IField::TYPE_SLUG         => 'umi\orm\metadata\field\special\SlugField',
        IField::TYPE_URI          => 'umi\orm\metadata\field\special\UriField',
        IField::TYPE_MPATH        => 'umi\orm\metadata\field\special\MaterializedPathField',
        IField::TYPE_LEVEL        => 'umi\orm\metadata\field\special\LevelField',
        IField::TYPE_ORDER        => 'umi\orm\metadata\field\special\OrderField',
        IField::TYPE_COUNTER      => 'umi\orm\metadata\field\special\CounterField',
        IField::TYPE_MONEY        => 'umi\orm\metadata\field\special\MoneyField',
        IField::TYPE_PASSWORD     => 'umi\orm\metadata\field\special\PasswordField',
        IField::TYPE_FILE         => 'umi\orm\metadata\field\special\FileField',
        IField::TYPE_IMAGE        => 'umi\orm\metadata\field\special\ImageField',
    ];

    /**
     * @var IDbCluster $dbCluster кластер
     */
    protected $dbCluster;

    /**
     * Конструктор фабрики метаданных.
     * @param IDbCluster $dbCluster кластер БД
     */
    public function __construct(IDbCluster $dbCluster)
    {
        $this->dbCluster = $dbCluster;
    }

    /**
     * {@inheritdoc}
     */
    public function create($collectionName, $config)
    {
        return $this->getPrototype(
                $this->metadataClassName,
                ['umi\orm\metadata\IMetadata']
            )
            ->createInstance([$collectionName, $config, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSource(array $config)
    {
        return $this->getPrototype(
                $this->dataSourceClass,
                ['umi\orm\metadata\ICollectionDataSource']
            )
            ->createInstance([$config, $this->dbCluster]);
    }

    /**
     * {@inheritdoc}
     */
    public function createObjectType($typeName, array $config, IMetadata $metadata)
    {
        return $this->getPrototype(
                $this->objectTypeClass,
                ['umi\orm\metadata\IObjectType']
            )
            ->createInstance([$typeName, $config, $metadata]);
    }

    /**
     * {@inheritdoc}
     */
    public function createField($fieldName, array $config)
    {
        if (!isset($config['type'])) {
            throw new UnexpectedValueException($this->translate(
                'Field configuration should contain type info.'
            ));
        }
        $fieldType = $config['type'];

        if (!isset($this->fieldTypes[$fieldType])) {
            throw new UnexpectedValueException($this->translate(
                'Cannot create field. Class for field type "{type}" is not defined.',
                ['type' => $fieldType]
            ));
        }

        return $this->getPrototype(
                $this->fieldTypes[$fieldType],
                ['umi\orm\metadata\field\IField']
            )
            ->createInstance([$fieldName, $config]);
    }
}

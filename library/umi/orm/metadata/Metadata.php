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
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\relation\HasManyRelationField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;

/**
 * Метаданные коллекции объектов (типы и поля).
 */
class Metadata implements IMetadata, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $collectionName имя коллекции объектов, которую описывает metadata
     */
    protected $collectionName;
    /**
     * @var IMetadataFactory $metadataFactory фабрика метаданных
     */
    protected $metadataFactory;
    /**
     * @var ICollectionDataSource $dataSource источник данных коллекции
     */
    protected $dataSource;
    /**
     * @var array $typesList список имен всех типов коллекции
     */
    protected $typesList;
    /**
     * @var array $fieldsList список имен всех полей коллекции
     */
    protected $fieldsList;
    /**
     * @var IObjectType[] $types массив всех загруженных экземпляров типов
     */
    protected $types = [];
    /**
     * @var IField[] $fields массив всех загруженных экземпляров полей
     */
    protected $fields = [];
    /**
     * @var array $config конфигурация
     */
    protected $config = [];

    /**
     * Конструктор.
     * @param string $collectionName имя коллекции объектов, которую описывает metadata
     * @param array $config конфигурация метаданных коллекции
     * @param IMetadataFactory $metadataFactory фабрика сущностей метаданных
     */
    public function __construct($collectionName, array $config, IMetadataFactory $metadataFactory)
    {
        $this->checkConfig($config);

        $this->config = $config;
        $this->collectionName = $collectionName;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionDataSource()
    {
        if (!$this->dataSource) {
            $this->dataSource = $this->metadataFactory->createDataSource($this->config['dataSource']);
        }

        return $this->dataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypesList()
    {
        if (is_null($this->typesList)) {
            $this->typesList = array_keys($this->config['types']);
        }

        return $this->typesList;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseType()
    {
        return $this->getType(IObjectType::BASE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExists($typeName)
    {
        return in_array($typeName, $this->getTypesList());
    }

    /**
     * {@inheritdoc}
     */
    public function getType($typeName)
    {
        if (isset($this->types[$typeName])) {
            return $this->types[$typeName];
        }
        if (!$this->getTypeExists($typeName)) {
            throw new NonexistentEntityException($this->translate(
                'Object type "{name}" does not exist in "{collection}".',
                ['name' => $typeName, 'collection' => $this->getCollectionName()]
            ));
        }
        $type = $this->metadataFactory->createObjectType($typeName, $this->config['types'][$typeName], $this);

        return $this->types[$typeName] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildTypesList($typeName = IObjectType::BASE)
    {
        return $this->getDescendantTypesList($typeName, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantTypesList($typeName = IObjectType::BASE, $depth = null)
    {
        if (!$this->getTypeExists($typeName)) {
            throw new NonexistentEntityException($this->translate(
                'Object type "{name}" does not exist in "{collection}".',
                ['name' => $typeName, 'collection' => $this->getCollectionName()]
            ));
        }
        $result = [];
        foreach ($this->getTypesList() as $name) {
            if ($name == $typeName) {
                continue;
            }
            if ($typeName == IObjectType::BASE
                && (is_null($depth) || (substr_count($name, IObjectType::NAME_SEPARATOR) + 1 <= $depth))
            ) {
                $result[] = $name;
            } elseif ($typeName != IObjectType::BASE && strpos($name, $typeName) === 0) {
                if (is_null($depth)
                    || (substr_count($name, IObjectType::NAME_SEPARATOR)
                        - substr_count($typeName, IObjectType::NAME_SEPARATOR) <= $depth)
                ) {
                    $result[] = $name;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsList()
    {
        if (is_null($this->fieldsList)) {
            $this->fieldsList = array_keys($this->config['fields']);
        }

        return $this->fieldsList;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $result = [];
        foreach ($this->getFieldsList() as $fieldName) {
            $result[$fieldName] = $this->getField($fieldName);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldExists($fieldName)
    {
        return in_array($fieldName, $this->getFieldsList());
    }

    /**
     * {@inheritdoc}
     */
    public function getField($fieldName)
    {
        if (isset($this->fields[$fieldName])) {
            return $this->fields[$fieldName];
        }
        if (!$this->getFieldExists($fieldName)) {
            throw new NonexistentEntityException($this->translate(
                'Field "{name}" does not exist in "{collection}".',
                ['name' => $fieldName, 'collection' => $this->getCollectionName()]
            ));
        }
        $field = $this->metadataFactory->createField($fieldName, $this->config['fields'][$fieldName]);

        return $this->fields[$fieldName] = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypesByFields(array $fieldNames, $typeName = IObjectType::BASE)
    {
        $types = [];

        $type = $this->getType($typeName);

        $success = true;
        foreach ($fieldNames as $fieldName) {
            if (!$type->getFieldExists($fieldName)) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $types = $this->getDescendantTypesList($typeName);
            $types[] = $typeName;
        } else {
            foreach ($this->getDescendantTypesList($typeName, 1) as $descendantTypeName) {
                $types = array_merge($types, $this->getTypesByFields($fieldNames, $descendantTypeName));
            }
        }

        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldByRelation($relatedFieldName, $bridgeCollectionName)
    {
        foreach ($this->getFieldsList() as $fieldName) {
            $field = $this->getField($fieldName);
            if ($field instanceof ManyToManyRelationField
                && $field->getRelatedFieldName() == $relatedFieldName
                && $field->getBridgeCollectionName() == $bridgeCollectionName
            ) {
                return $field;
            }
        }
        throw new NonexistentEntityException($this->translate(
            'Cannot find field with relation bridge "{bridge}" and related field "{relatedField}".',
            ['bridge' => $bridgeCollectionName, 'relatedField' => $relatedFieldName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldByTarget($targetFieldName, $targetCollectionName)
    {
        foreach ($this->getFieldsList() as $fieldName) {
            /** @var $field HasManyRelationField|ManyToManyRelationField */
            $field = $this->getField($fieldName);
            if (($field instanceof HasManyRelationField || $field instanceof ManyToManyRelationField)
                && $field->getTargetFieldName() == $targetFieldName
                && $field->getTargetCollectionName() == $targetCollectionName
            ) {
                return $field;
            }
        }
        throw new NonexistentEntityException($this->translate(
            'Cannot find field with relation target "{target}" and target field "{targetField}".',
            ['target' => $targetCollectionName, 'targetField' => $targetFieldName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParentType($typeName)
    {
        if (!$this->getTypeExists($typeName)) {
            throw new NonexistentEntityException($this->translate(
                'Object type "{name}" does not exist in "{collection}".',
                ['name' => $typeName, 'collection' => $this->getCollectionName()]
            ));
        }

        $parentTypeName = $this->getParentTypeName($typeName);
        if (is_null($parentTypeName)) {
            return null;
        }

        return $this->getType($parentTypeName);
    }

    /**
     * Возвращает имя непосредственного родителя типа либо null, если родителя не существует
     * @param string $typeName имя типа
     * @return string
     */
    protected function getParentTypeName($typeName)
    {
        if ($typeName == IObjectType::BASE) {
            return null;
        }

        if (strpos($typeName, IObjectType::NAME_SEPARATOR) === false) {
            return IObjectType::BASE;
        }

        $parentTypes = explode(IObjectType::NAME_SEPARATOR, $typeName);
        array_pop($parentTypes);

        return implode(IObjectType::NAME_SEPARATOR, $parentTypes);
    }

    /**
     * Возвращает проверенную конфигурацию метаданных в виде массива.
     * @param array $config конфигурация метаданных
     * @throws UnexpectedValueException если структура конфигурации не соответсвует ожидаемой
     */
    protected function checkConfig(array $config)
    {
        if (!isset($config['dataSource']) || !is_array($config['dataSource'])) {
            throw new UnexpectedValueException($this->translate(
                'Information about data source in metadata configuration should be an array or Traversable.'
            ));
        }
        if (!isset($config['types']) || !is_array($config['types'])) {
            throw new UnexpectedValueException($this->translate(
                'Information about types in metadata configuration should be an array or Traversable.'
            ));
        }
        if (!isset($config['fields']) || !is_array($config['fields'])) {
            throw new UnexpectedValueException($this->translate(
                'Information about fields in metadata configuration should be an array or Traversable.'
            ));
        }
    }
}

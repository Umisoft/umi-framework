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
use umi\orm\metadata\field\ILocalizableField;

/**
 * Тип данных.
 */
class ObjectType implements IObjectType, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $name имя типа
     */
    protected $name;
    /**
     * @var IMetadata $metadata
     */
    protected $metadata;
    /**
     * @var string $objectClassName полное квалифицированное имя класса
     */
    protected $objectClassName;
    /**
     * @var IField[] $fields список экземпляров полей типа, массив вида array(fieldName => IField, ...)
     */
    protected $fields = [];

    /**
     * Конструктор.
     * @param string $name имя
     * @param array $config конфигурация
     * @param IMetadata $metadata метаданные, к которым относится тип
     * @throws UnexpectedValueException в случае неверно заданной конфигурации
     */
    public function __construct($name, array $config, IMetadata $metadata)
    {

        $this->name = $name;
        $this->metadata = $metadata;
        if (isset($config['objectClass'])) {
            $this->objectClassName = strval($config['objectClass']);
        } else {
            if ($parentType = $metadata->getParentType($this->name)) {
                $this->objectClassName = $parentType->getObjectClass();
            }
        }

        if (isset($config['fields'])) {
            $fieldsInfo = $config['fields'];
            if (!is_array($fieldsInfo)) {
                throw new UnexpectedValueException($this->translate(
                    'Type fields configuration should be an array.'
                ));
            }
            foreach ($fieldsInfo as $fieldName) {
                $field = $metadata->getField($fieldName);
                $this->fields[$fieldName] = $field;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectClass()
    {
        return $this->objectClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedFields()
    {
        $localizedFields = [];

        foreach ($this->getFields() as $field) {
            if ($field instanceof ILocalizableField && $field->getIsLocalized()) {
                $localizedFields[$field->getName()] = $field;
            }
        }

        return $localizedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldExists($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getField($fieldName)
    {
        if (!$this->getFieldExists($fieldName) || !$field = $this->fields[$fieldName]) {
            throw new NonexistentEntityException($this->translate(
                'Field "{name}" does not exist in "{collection}.{type}".',
                ['name' => $fieldName, 'collection' => $this->metadata->getCollectionName(), 'type' => $this->name]
            ));
        }

        return $field;
    }
}

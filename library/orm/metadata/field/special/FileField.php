<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\special;

use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\exception\UnexpectedValueException;
use umi\orm\metadata\field\BaseField;
use umi\orm\object\IObject;
use umi\orm\object\property\file\File;
use umi\orm\object\property\file\IFileProperty;

/**
 * Класс поля для файла.
 */
class FileField extends BaseField
{

    /**
     * @var string $sourcePath абсолютный путь к директории, где хранятся файлы для этого поля
     */
    protected $sourcePath;
    /**
     * @var string $sourceURI абсолютный uri к директории с файлами
     */
    protected $sourceURI;

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        throw new NotAllowedOperationException($this->translate(
            'Cannot set value for property "{name}".',
            ['name' => $this->getName()]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        /**
         * @var IFileProperty $fileProperty
         */
        $fileProperty = $object->getProperty($this->getName());
        return new File($fileProperty, $this->sourcePath, $this->sourceURI, $internalDbValue);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        $dbValue = null;
        if ($propertyValue instanceof File) {

            try {
                $filePath = $propertyValue->getFileInfo()->getRealPath();
                if ($filePath) {
                    $dbValue = str_replace($this->sourcePath . '/', '', $filePath);
                }
            } catch(RuntimeException $e) {

            }
        }

        return $dbValue;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyConfiguration(array $config)
    {
        if (!isset($config['sourcePath']) || !is_string($config['sourcePath'])) {
            throw new UnexpectedValueException($this->translate(
                'File field configuration should contain source path and path should be a string.'
            ));
        }
        $this->sourcePath = $config['sourcePath'];

        if (!isset($config['sourceURI']) || !is_string($config['sourceURI'])) {
            throw new UnexpectedValueException($this->translate(
                'File field configuration should contain directory URI and URI should be a string.'
            ));
        }
        $this->sourceURI = $config['sourceURI'];

    }
}

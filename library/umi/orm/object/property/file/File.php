<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property\file;

use umi\orm\exception\InvalidArgumentException;
use umi\orm\exception\RuntimeException;

/**
 * Значение свойства типа файл.
 */
class File
{

    /**
     * @var string $sourcePath абсолютный путь к директории, где хранится файл
     */
    protected $sourcePath;
    /**
     * @var string $sourceURI абсолютный uri директории с файлом
     */
    protected $sourceURI;
    /**
     * @var string $fileURI абсолютный uri файла
     */
    protected $fileURI;
    /**
     * @var IFileProperty $property свойство, значением которого является файл
     */
    protected $property;
    /**
     * @var \SplFileInfo $splFileInfo
     */
    protected $splFileInfo;

    /**
     * Конструктор.
     * @param IFileProperty $property свойство, значением которого является файл
     * @param string $sourcePath абсолютный путь к директории, где хранится файл
     * @param string $sourceURI абсолютный uri к директории с файлом
     * @param string|null $relativePath путь к файлу относительного его директории
     */
    public function __construct(IFileProperty $property, $sourcePath, $sourceURI, $relativePath = null)
    {
        $this->sourcePath = $sourcePath;
        $this->sourceURI = $sourceURI;
        $this->property = $property;

        if ($relativePath) {
            $this->splFileInfo = new \SplFileInfo($sourcePath . '/' . $relativePath);
            $this->fileURI = $this->sourceURI . '/' . $relativePath;
        }
    }

    /**
     * Возвращает информацию о файле.
     * @throws RuntimeException если путь к файлу не был задан
     * @return \SplFileInfo
     */
    public function getFileInfo()
    {
        if (is_null($this->splFileInfo)) {
            throw new RuntimeException('Cannot get file info. File path is not defined');
        }
        return $this->splFileInfo;
    }

    /**
     * Устанавливает путь к файлу.
     * @param string $filePath путь к файлу
     * @throws InvalidArgumentException если файл не существует или находится не в заданной директории
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $splFileInfo = new \SplFileInfo($filePath);
        if (!$realFilePath = $splFileInfo->getRealPath()) {
            throw new InvalidArgumentException(
                sprintf('Cannot set file path. File "%s" does not exist.', $filePath)
            );
        }

        if (strpos($realFilePath, $this->sourcePath) !== 0) {
            throw new InvalidArgumentException(
                sprintf('Cannot set file path. File path "%s" does not match source path.', $filePath)
            );
        }

        if (is_null($this->splFileInfo) || $this->splFileInfo->getRealPath() != $realFilePath) {

            $this->splFileInfo = $splFileInfo;

            $relativePath = str_replace($this->sourcePath, '', $realFilePath);
            $this->fileURI = $this->sourceURI . $relativePath;

            $this->property->update();
        }

        return $this;
    }

    /**
     * Возвращает URI файла.
     * @throws RuntimeException если путь к файлу не был задан
     * @return string
     */
    public function getURI()
    {
        if (!$this->fileURI) {
            throw new RuntimeException('Cannot get file URI. File path is not defined');
        }
        return $this->fileURI;
    }

    /**
     *
     * @return $this
     */
    public function clear()
    {
        $this->splFileInfo = null;
        $this->fileURI = null;
        $this->property->update();

        return $this;
    }

}
 
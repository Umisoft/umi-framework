<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\templating\exception\RuntimeException;
use umi\templating\extension\adapter\IExtensionAdapter;

/**
 * Базовый класс шаблонизатора.
 */
abstract class BaseTemplateEngine implements ITemplateEngine, ILocalizable
{
    use TLocalizable;

    /**
     * @var string $extension [optional] расширение файлов шаблонов
     */
    protected $fileExtension;
    /**
     * @var IExtensionAdapter $extensionAdapter
     */
    private $extensionAdapter;

    /**
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options)
    {
        $this->fileExtension = isset($options[self::OPTION_EXTENSION]) ? $options[self::OPTION_EXTENSION] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAdapter(IExtensionAdapter $adapter)
    {
        $this->extensionAdapter = $adapter;

        return $this;
    }

    /**
     * Возвращает адаптер для расширения шаблонизатора.
     * @return IExtensionAdapter
     * @throws RuntimeException если адаптер расширения не установлен
     */
    protected final function getExtensionAdapter()
    {
        if (!$this->extensionAdapter) {
            throw new RuntimeException($this->translate(
                'Invalid extension adapter specified.'
            ));
        }

        return $this->extensionAdapter;
    }

    /**
     * Возрващает имя файла шаблона по имени шаблона.
     * @param string $name имя шаблона
     * @return string
     */
    protected function getTemplateFilename($name)
    {
        if (!is_null($this->fileExtension)) {
            $name .= '.' . $this->fileExtension;
        }

        return $name;
    }
}
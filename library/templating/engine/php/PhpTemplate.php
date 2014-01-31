<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\engine\php;

use umi\templating\exception\RuntimeException;

/**
 * PHP шаблонизатор.
 */
class PhpTemplate
{
    /**
     * @var string $_templatingDirectory директория с шаблонами
     */
    protected $_templatingDirectory;
    /**
     * @var callable $_templatingHelperCallback callback для вызова помощников
     */
    protected $_templatingHelperCallback;

    /**
     * Конструктор
     * @param string $directory
     * @param callable $helperCallback
     */
    public function __construct($directory = '.', callable $helperCallback = null)
    {
        $this->_templatingDirectory = $directory;
        $this->_templatingHelperCallback = $helperCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function render($_templateName, array $_templateVariables = [])
    {
        $_templateFilename = $this->_templatingDirectory . DIRECTORY_SEPARATOR . $_templateName;

        if (!is_readable($_templateFilename)) {
            throw new RuntimeException(sprintf(
                'Cannot render template "%s". PHP template file "%s" is not readable.',
                $_templateName,
                $_templateFilename
            ));
        }

        extract($_templateVariables);

        ob_start();
        try {
            /** @noinspection PhpIncludeInspection */
            require $_templateFilename;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }

    /**
     * Magic method: вызывает помощник шаблонов.
     * @param string $name имя помощника шаблонов
     * @param array $arguments аргументы
     * @return string
     */
    public function __call($name, array $arguments)
    {
        return call_user_func($this->_templatingHelperCallback, $name, $arguments);
    }
}
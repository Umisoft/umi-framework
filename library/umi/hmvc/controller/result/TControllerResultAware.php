<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\result;

use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для внедрения возможности создания результатов работы контроллера.
 */
trait TControllerResultAware
{
    /**
     * @var IControllerResultFactory $_hmvcControllerResultFactory фабрика
     */
    private $_hmvcControllerResultFactory;

    /**
     * Устанавливает фабрику результатов работы контроллера.
     * @param IControllerResultFactory $factory фабрика
     */
    public final function setControllerResultFactory(IControllerResultFactory $factory)
    {
        $this->_hmvcControllerResultFactory = $factory;
    }

    /**
     * Создает результат работы контроллера.
     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return IControllerResult
     */
    protected final function createControllerResult($template, array $variables = [])
    {
        return $this->getMvcControllerResultFactory()
            ->createControllerResult($template, $variables);
    }

    /**
     * Возвращает фабрику результатов работы контроллера.
     * @return IControllerResultFactory фабрика
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getMvcControllerResultFactory()
    {
        if (!$this->_hmvcControllerResultFactory) {
            throw new RequiredDependencyException(sprintf(
                'HMVC controller result factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_hmvcControllerResultFactory;
    }
}
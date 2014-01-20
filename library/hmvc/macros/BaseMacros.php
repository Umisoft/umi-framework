<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\macros;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\response\HTTPComponentResponse;
use umi\hmvc\component\response\IHTTPComponentResponse;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\view\content\Content;

/**
 * Базовая реализация макроса компонента.
 */
abstract class BaseMacros implements IMacros
{
    /**
     * @var IComponent $component компонент, которому принадлежит контроллер
     */
    private $component;

    /**
     * {@inheritdoc}
     */
    public function setComponent(IComponent $component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Возвращает компонент, которому принадлежит контроллер.
     * @throws RequiredDependencyException если контроллер не был установлен
     * @return IComponent
     */
    protected function getComponent()
    {
        if (!$this->component) {
            throw new RequiredDependencyException(
                sprintf('Component is not injected in controller "%s".', __CLASS__)
            );
        }

        return $this->component;
    }

    /**
     * Создает результат работы макроса, не требующий шаблонизации.
     * @param string $content содержимое ответа
     * @return IHTTPComponentResponse
     */
    protected function createPlainResponse($content)
    {
        return $this->createMacrosResult()
            ->setContent($content);
    }

    /**
     * Создает результат работы макроса, требующий шаблонизации.
     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return IHTTPComponentResponse
     */
    protected function createDisplayResponse($template, array $variables)
    {
        return $this->createMacrosResult()
            ->setContent(
                new Content($this->getComponent()->getView(), $template, $variables)
            );
    }

    /**
     * Возвращает фабрику для результатов работы макроса.
     * @throws RequiredDependencyException если фабрика не была внедрена
     * @return IHTTPComponentResponse
     */
    private function createMacrosResult()
    {
        return new HTTPComponentResponse($this->getComponent());
    }

}

 
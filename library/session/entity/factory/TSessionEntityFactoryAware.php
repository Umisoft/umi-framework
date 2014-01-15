<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\factory;

use SessionHandlerInterface;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\entity\validator\ISessionValidator;
use umi\session\exception\RequiredDependencyException;

/**
 * Трейт для внедрения возможности создания пространств имен в сессии.
 */
trait TSessionEntityFactoryAware
{
    /**
     * @var ISessionEntityFactory $_namespaceFactory
     */
    private $_namespaceFactory;

    /**
     * Устанавливает фабрику для создания простанств имен.
     * @param ISessionEntityFactory $nsFactory фабрика
     */
    public final function setNamespaceFactory(ISessionEntityFactory $nsFactory)
    {
        $this->_namespaceFactory = $nsFactory;
    }

    /**
     * Создает пространство имен сессии.
     * @param string $name имя
     * @return ISessionNamespace
     */
    protected final function createSessionNamespace($name)
    {
        return $this->getNamespaceFactory()
            ->createSessionNamespace($name);
    }

    /**
     * Создает валидатор сессии заданного типа.
     * @param string $type тип валидатора
     * @param array|mixed $options опции валидатора
     * @return ISessionValidator
     */
    protected final function createSessionValidator($type, $options)
    {
        return $this->getNamespaceFactory()
            ->createSessionValidator($type, $options);
    }

    /**
     * Создает объект хранилища сессии.
     * @param string $type тип хранилища
     * @param array $options опции хранилища
     * @return SessionHandlerInterface
     */
    protected final function createSessionStorage($type, array $options = [])
    {
        return $this->getNamespaceFactory()
            ->createSessionStorage($type, $options);
    }

    /**
     * Возвращает фабрику пространства имен.
     * @return ISessionEntityFactory
     * @throws RequiredDependencyException
     */
    private final function getNamespaceFactory()
    {
        if (!$this->_namespaceFactory) {
            throw new RequiredDependencyException(sprintf(
                'Session namespace factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_namespaceFactory;
    }
}
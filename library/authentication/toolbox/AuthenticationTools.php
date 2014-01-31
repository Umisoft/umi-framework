<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox;

use umi\authentication\IAuthenticationAware;
use umi\authentication\IAuthenticationFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты аутентификации.
 */
class AuthenticationTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'authentication';

    use TToolbox;

    /**
     * @var string $authenticationFactoryClass класс фабрики объектов аутентификации
     */
    public $authenticationFactoryClass = 'umi\authentication\toolbox\factory\AuthenticationFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'authentication',
            $this->authenticationFactoryClass,
            ['umi\authentication\IAuthenticationFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\authentication\IAuthenticationFactory':
                return $this->getAuthenticationFactory();
            case 'umi\authentication\IAuthManager':
                return $this->getAuthenticationFactory()
                    ->getDefaultAuthManager();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IAuthenticationAware) {
            $object->setAuthenticationFactory($this->getAuthenticationFactory());
        }
    }

    /**
     * Возвращает фабрику объектов аутентификациию
     * @return IAuthenticationFactory
     */
    protected function getAuthenticationFactory()
    {
        return $this->getFactory('authentication');
    }
}

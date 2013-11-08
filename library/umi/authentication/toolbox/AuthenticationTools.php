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
use umi\authentication\result\IAuthenticationResultAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты аутентификации.
 */
class AuthenticationTools implements IAuthenticationTools
{

    use TToolbox;

    /**
     * @var string $authenticationFactoryClass класс фабрики объектов аутентификации
     */
    public $authenticationFactoryClass = 'umi\authentication\toolbox\factory\AuthenticationFactory';
    /**
     * @var string $authenticationResultFactoryClass класс фабрики результатов аутентификации
     */
    public $authenticationResultFactoryClass = 'umi\authentication\toolbox\factory\AuthenticationResultFactory';

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

        $this->registerFactory(
            'result',
            $this->authenticationResultFactoryClass,
            ['umi\authentication\result\IAuthenticationResultFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IAuthenticationAware) {
            $object->setAuthenticationFactory($this->getAuthenticationFactory());
        }

        if ($object instanceof IAuthenticationResultAware) {
            $object->setAuthenticationResultFactory($this->getAuthenticationResultFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationFactory()
    {
        return $this->getFactory('authentication');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthenticationResultFactory()
    {
        return $this->getFactory('result');
    }
}
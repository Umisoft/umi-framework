<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox\factory;

use umi\authentication\result\IAuthenticationResultFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика результатов аутентификации
 */
class AuthenticationResultFactory implements IAuthenticationResultFactory, IFactory
{

    use TFactory;

    /**
     * @var string $resultClass класс менеджера
     */
    public $resultClass = 'umi\authentication\result\AuthResult';

    /**
     * {@inheritdoc}
     */
    public function createResult($status, $identity = null)
    {
        return $this->createInstance(
            $this->resultClass,
            [$status, $identity],
            ['umi\authentication\result\IAuthResult']
        );
    }
}

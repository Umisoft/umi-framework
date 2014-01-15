<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\provider;

use umi\http\request\IRequest;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Провайдер HTTP авторизации.
 */
class HttpProvider implements IAuthProvider, ILocalizable
{

    use TLocalizable;

    /**
     * @var IRequest $request HTTP запрос
     */
    protected $request = null;

    /**
     * Конструктор.
     * @param IRequest $request HTTP запрос
     */
    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        $username = $this->request->getVar(IRequest::HEADERS, 'PHP_AUTH_USER');
        $password = $this->request->getVar(IRequest::HEADERS, 'PHP_AUTH_PW');

        if ($username && $password) {
            return [$username, $password];
        } else {
            return false;
        }
    }
}
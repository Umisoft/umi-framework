<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\validator;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\session\entity\ns\ISessionNamespace;

/**
 * Валидатор User Agent.
 */
class UserAgent implements ISessionValidator, ILocalizable
{

    use TLocalizable;

    /**
     * Ключ в метаданных для хранения параметра User Agent последнего доступа.
     */
    const META_KEY = 'UserAgent';

    /**
     * {@inheritdoc}
     */
    public function validate(ISessionNamespace $session)
    {
        if (!$session->getMetadata(self::META_KEY)) {
            $session->setMetadata(self::META_KEY, $this->getUserAgent());
        }

        return $session->getMetadata(self::META_KEY) === $this->getUserAgent();
    }

    /**
     * Возвращает UserAgent из запроса
     * @return string
     */
    protected function getUserAgent()
    {
        return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
    }
}
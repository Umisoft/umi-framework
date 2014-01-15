<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\validator;

use umi\session\entity\ns\ISessionNamespace;

/**
 * Интерфейс валидаторов для сессии.
 */
interface ISessionValidator
{
    /**
     * Валидатор времени жизни сессии.
     */
    const LIFE_TIME = 'LifeTime';
    /**
     * Валидатор User Agent браузера.
     */
    const USER_AGENT = 'UserAgent';

    /**
     * Проверяет валидность сессии, либо контейнера сессии
     * @param ISessionNamespace $namespace
     * @return bool
     */
    public function validate(ISessionNamespace $namespace);
}
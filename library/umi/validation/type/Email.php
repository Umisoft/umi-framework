<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\type;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\validation\IValidator;

/**
 * Валидатор E-mail.
 */
class Email implements IValidator, ILocalizable
{

    use TLocalizable;

    /**
     * @var array $messages ошибки валидации
     */
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $this->messages = [];

        if ($value !== null && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->messages[] = $this->translate('Wrong email format.');

            return false;
        }

        return true;
    }
}
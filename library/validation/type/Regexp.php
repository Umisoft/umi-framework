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
use umi\validation\exception\RuntimeException;
use umi\validation\IValidator;

/**
 * Валидатор по регулярному выражению.
 */
class Regexp implements IValidator, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $pattern регулярное выражение
     */
    protected $pattern = null;
    /**
     * @var array $messages ошибки валидации
     */
    protected $messages = [];

    /**
     * Конструктор.
     * @param array $options опции валидатора
     * @throws RuntimeException если регулярное выражение не задано
     */
    public function __construct(array $options)
    {
        if (empty($options['pattern'])) {
            throw new RuntimeException($this->translate(
                'No regular expression pattern.'
            ));
        }
        $this->pattern = $options['pattern'];
    }

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

        $valid = filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $this->pattern]]) !== false;

        if (!$valid) {
            $this->messages[] = $this->translate('String does not meet regular expression.');
        }

        return $valid;
    }
}
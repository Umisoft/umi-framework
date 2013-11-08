<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\element\html5;

use umi\form\element\Text;
use umi\validation\IValidatorFactory;

/**
 * HTML5 элемент формы - Email (email).
 * @example <input type="email" />
 */
class Email extends Text
{
    /**
     * Тип элемента.
     */
    const TYPE_NAME = 'email';

    /**
     * @var array $attributes аттрибуты
     */
    protected $attributes = [
        'type' => self::TYPE_NAME
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($name, array $attributes = [], array $options = [])
    {
        parent::__construct($name, $attributes, $options);

        $this->getValidators()
            ->prependValidator($this->createValidator(IValidatorFactory::TYPE_EMAIL));
    }
}
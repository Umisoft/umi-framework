<?php

use umi\filter\IFilterFactory;
use umi\form\element\Password;
use umi\form\element\Text;
use umi\form\fieldset\Collection;
use umi\form\Form;
use umi\validation\IValidatorFactory;

return [
    'name'     => 'register',
    'action'   => '/user/register',
    'method'   => 'post',
    'elements' => [
        'email'    => [
            'type'       => Text::TYPE_NAME,
            'label'      => 'E-mail',
            'filters'    => [
                IFilterFactory::TYPE_STRING_TRIM => []
            ],
            'validators' => [
                IValidatorFactory::TYPE_REQUIRED => [],
                IValidatorFactory::TYPE_EMAIL    => []
            ]
        ],
        'password' => [
            'type'  => Password::TYPE_NAME,
            'label' => 'Пароль'
        ],
        'passport' => [
            'type'     => Form::TYPE_NAME,
            'label'    => 'Место жительства',
            'elements' => [
                'number'        => [
                    'type'  => 'text',
                    'label' => 'Номер пасспорта'
                ],
                'birthday_city' => [
                    'type'       => Text::TYPE_NAME,
                    'label'      => 'Город рождения',
                    'attributes' => [
                        'name' => 'city'
                    ]
                ]
            ]
        ],
        'fieldset' => [
            'elements' => [
                'fieldInFieldset' => [
                    'type' => Text::TYPE_NAME
                ]
            ]
        ],
        'scans'    => [
            'type'     => Collection::TYPE_NAME,
            'elements' => [
                [
                    'type' => Text::TYPE_NAME,
                ]
            ]
        ],
        'submit'   => [
            'type'  => 'submit',
            'label' => 'Зарегистрироваться'
        ]
    ]
];
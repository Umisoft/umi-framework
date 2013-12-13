<?php

use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

return [
    'dataSource' => [
        'sourceName' => 'umi_mock_users'
    ],
    'fields'     => [
        IObject::FIELD_IDENTIFY => [
            'type'          => IField::TYPE_IDENTIFY,
            'columnName'    => 'id',
            'accessor'      => 'getId'
        ],
        IObject::FIELD_GUID     => [
            'type'       => IField::TYPE_GUID,
            'columnName' => 'guid',
            'accessor'   => 'getGuid',
            'mutator'    => 'setGuid'
        ],
        IObject::FIELD_TYPE     => [
            'type'       => IField::TYPE_STRING,
            'columnName' => 'type',
            'accessor'   => 'getType',
            'readOnly'   => true
        ],
        IObject::FIELD_VERSION  => [
            'type'         => IField::TYPE_VERSION,
            'columnName'   => 'version',
            'accessor'     => 'getVersion',
            'mutator'      => 'setVersion',
            'defaultValue' => 1
        ],
        'login'                 => [
            'type'       => IField::TYPE_STRING,
            'columnName' => 'login',
            'accessor'   => 'getLogin',
            'mutator'    => 'setLogin',
            'validators' => ['required' => []]
        ]
    ],
    'types'      => [
        'base' => [
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'login'
            ]
        ]
    ]
];
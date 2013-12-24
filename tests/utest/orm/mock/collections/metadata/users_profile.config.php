<?php
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

return [
    'dataSource' => [
        'sourceName' => 'umi_mock_profiles'
    ],
    'fields'     => [
        IObject::FIELD_IDENTIFY => ['type' => IField::TYPE_IDENTIFY, 'columnName' => 'id', 'accessor' => 'getId'],
        IObject::FIELD_GUID     => ['type'       => IField::TYPE_GUID,
                                    'columnName' => 'guid',
                                    'accessor'   => 'getGuid',
                                    'mutator'    => 'setGuid'
        ],
        IObject::FIELD_TYPE     => ['type'       => IField::TYPE_STRING,
                                    'columnName' => 'type',
                                    'accessor'   => 'getType',
                                    'readOnly'   => true
        ],
        IObject::FIELD_VERSION  => ['type'         => IField::TYPE_VERSION,
                                    'columnName'   => 'version',
                                    'accessor'     => 'getVersion',
                                    'mutator'      => 'setVersion',
                                    'defaultValue' => 1
        ],
        'name'                  => ['type' => IField::TYPE_STRING, 'columnName' => 'name'],
        'organizationName'      => ['type' => IField::TYPE_STRING, 'columnName' => 'org_name'],
        'city'                  => ['type'       => IField::TYPE_BELONGS_TO,
                                    'columnName' => 'city_id',
                                    'target'     => 'guides_city'
        ],
        'user'                  => ['type'       => IField::TYPE_BELONGS_TO,
                                    'columnName' => 'user_id',
                                    'target'     => 'users_user'
        ],
        'image'                 => ['type' => IField::TYPE_FILE, 'columnName' => 'image', 'sourcePath' => TESTS_ROOT . '/utest/orm/mock/files', 'sourceURI' => 'http://example.com']
    ],
    'types'      => [
        'base'           => [
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'city',
                'user'
            ]
        ],
        'natural_person' => [
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'city',
                'user',
                'name',
                'image'
            ],
        ],
        'legal_person'   => [
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'city',
                'user',
                'organizationName'
            ]
        ]
    ]
];

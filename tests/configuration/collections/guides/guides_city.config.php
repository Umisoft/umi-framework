<?php
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

return [
    'dataSource' => [
        'sourceName' => 'umi_mock_cities'
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
        'country'               => ['type'       => IField::TYPE_BELONGS_TO,
                                    'columnName' => 'country_id',
                                    'target'     => 'guides_country'
        ]
    ],
    'types'      => [
        'base' => [
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'name',
                'country'
            ]
        ]
    ]
];

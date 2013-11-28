<?php
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

return [
    'dataSource' => [
        'sourceName' => 'umi_mock_users'
    ],
    'fields' => [
        IObject::FIELD_IDENTIFY => ['type' => IField::TYPE_IDENTIFY, 'columnName' => 'id', 'accessor' => 'getId'],
        IObject::FIELD_GUID => ['type' => IField::TYPE_GUID, 'columnName' => 'guid', 'accessor' => 'getGuid', 'mutator' => 'setGuid'],
        IObject::FIELD_TYPE => ['type' => IField::TYPE_STRING, 'columnName' => 'type', 'accessor' => 'getType', 'readOnly' => true],
        IObject::FIELD_VERSION => ['type' => IField::TYPE_VERSION, 'columnName' => 'version', 'accessor' => 'getVersion', 'mutator' => 'setVersion', 'defaultValue' => 1],

        'login' => ['type' => IField::TYPE_STRING, 'columnName' => 'login', 'accessor' => 'getLogin', 'mutator' => 'setLogin', 'validators' => ['required' => []]],
        'email' => ['type' => IField::TYPE_STRING, 'columnName' => 'email', 'validators' => ['email' => []]],
        'password' => ['type' => IField::TYPE_PASSWORD, 'columnName' => 'password', 'accessor' => 'getPassword', 'mutator' => 'setPassword'],
        'isActive' => ['type' => IField::TYPE_BOOL, 'columnName' => 'is_active', 'defaultValue' => 1],

        'rating' => ['type' => IField::TYPE_REAL, 'dataType' => 'float', 'columnName' => 'rating', 'defaultValue' => 0],
        'height' => ['type' => IField::TYPE_INTEGER, 'columnName' => 'height', 'validators' => ['required' => [], 'regexp' => ['pattern' => '/[0-9]{2,3}/']]],

        'group' => ['type' => IField::TYPE_BELONGS_TO, 'columnName' => 'group_id', 'target' => 'users_group'],
        'profile' => ['type' => IField::TYPE_HAS_ONE, 'target' => 'users_profile', 'targetField' => 'user'],
        'blogs' => ['type' => IField::TYPE_HAS_MANY, 'target' => 'blogs_blog', 'targetField' => 'owner'],
        'subscription' => ['type' => IField::TYPE_MANY_TO_MANY, 'target' => 'blogs_blog', 'bridge' => 'blogs_blog_subscribers', 'relatedField' => 'user', 'targetField' => 'blog'],

        'supervisorField' => ['type' => IField::TYPE_INTEGER, 'columnName' => 'supervisor_field'],
        'guestField' => ['type' => IField::TYPE_INTEGER, 'columnName' => 'guest_field']
    ],
    'types' => [
        'base' => [
            'objectClass' => 'utest\orm\mock\collections\User',
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'login', 'email', 'password', 'isActive', 'rating', 'height', 'group', 'profile', 'blogs', 'subscription'
            ]
        ],
        'guest' => [
            'objectClass' => 'utest\orm\mock\collections\User',
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'login', 'email', 'password', 'isActive', 'rating', 'height', 'group', 'profile', 'blogs', 'subscription', 'guestField'
            ]
        ],
        'supervisor' => [
            'objectClass' => 'utest\orm\mock\collections\Supervisor',
            'fields' => [
                IObject::FIELD_IDENTIFY,
                IObject::FIELD_GUID,
                IObject::FIELD_TYPE,
                IObject::FIELD_VERSION,
                'login', 'email', 'password', 'isActive', 'rating', 'height', 'group', 'profile', 'blogs', 'subscription', 'supervisorField'
            ]
        ]
    ]
];
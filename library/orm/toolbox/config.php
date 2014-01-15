<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox;

/**
 * Конфигурация для регистрации набора инструментов.
 */
return [
    'name'    => ORMTools::NAME,
    'class'        => __NAMESPACE__ . '\ORMTools',
    'awareInterfaces' => [
        'umi\orm\collection\ICollectionManagerAware',
        'umi\orm\manager\IObjectManagerAware',
        'umi\orm\metadata\IMetadataManagerAware',
        'umi\orm\persister\IObjectPersisterAware'
    ],
    'services' => [
        'umi\orm\collection\ICollectionManager',
        'umi\orm\manager\IObjectManager',
        'umi\orm\metadata\IMetadataManager',
        'umi\orm\persister\IObjectPersister'
    ]
];

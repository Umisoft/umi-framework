<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\tests\configuration;

use umi\config\toolbox\ConfigTools;
use umi\i18n\toolbox\I18nTools;

$libraryPath = realpath(__DIR__ . '/../../library/umi');

/**
 * Конфигурация для регистрации наборов инструментов,
 * используемых в тестировании
 */

return [
    'toolkit'  => [
        require($libraryPath . '/config/toolbox/config.php'),
        require($libraryPath . '/i18n/toolbox/config.php'),
        require($libraryPath . '/dbal/toolbox/config.php'),
        require($libraryPath . '/orm/toolbox/config.php'),
        require($libraryPath . '/pagination/toolbox/config.php'),
        require($libraryPath . '/rbac/toolbox/config.php'),
        require($libraryPath . '/route/toolbox/config.php'),
        require($libraryPath . '/validation/toolbox/config.php'),
        require($libraryPath . '/templating/toolbox/config.php'),
    ],
    'settings' => [
        ConfigTools::NAME       => [
            'aliases' => [
                '~' => [TESTS_CONFIGURATION, TESTS_CONFIGURATION . '/local']
            ]
        ],
        I18nTools::NAME => [
            'defaultLocale' => 'en-US',
            'currentLocale' => 'ru-RU'
        ]
    ]
];

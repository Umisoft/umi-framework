<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\session;

use umi\session\toolbox\SessionTools;
use umi\toolkit\IToolkit;

/**
 * Трейт для регистрации тулбокса сессий
 */
trait TSessionSupport
{
    /**
     * Получить тестовый тулкит
     * @throws \RuntimeException
     * @return IToolkit
     */
    abstract protected function getTestToolkit();

    protected function registerSessionTools()
    {
        $this->getTestToolkit()->registerToolbox(
            require(LIBRARY_PATH . '/session/toolbox/config.php')
        );

        $this->getTestToolkit()->setSettings(
            [
                SessionTools::NAME => [
                    'factories' => [
                        'entity' => [
                            'validatorClasses' => [
                                'mock' => 'utest\session\mock\validator\MockSessionValidator'
                            ],
                            'storageClasses'   => [
                                'null' => 'utest\session\mock\storage\Null'
                            ]
                        ]
                    ]
                ]
            ]
        );
    }
}
 
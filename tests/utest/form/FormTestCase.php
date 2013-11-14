<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\form;

use utest\TestCase;

/**
 * Тест кейс для форм
 */
abstract class FormTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {

        $this->getTestToolkit()->registerToolboxes([
            require(LIBRARY_PATH . '/event/toolbox/config.php'),
            require(LIBRARY_PATH . '/filter/toolbox/config.php'),
            require(LIBRARY_PATH . '/http/toolbox/config.php'),
            require(LIBRARY_PATH . '/session/toolbox/config.php'),
            require(LIBRARY_PATH . '/form/toolbox/config.php')
        ]);

        parent::setUp();
    }
}
 
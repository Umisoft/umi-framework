<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

$vendorDir = dirname(__DIR__) . '/vendor';

if (!is_readable($vendorDir . '/autoload.php')) {
    throw new \RuntimeException('Composer autoloader not found. Run composer install.');
}

if (!defined('TESTS_ROOT')) {
    define('TESTS_ROOT', __DIR__);
}

if (!defined('TESTS_CONFIGURATION')) {
    define('TESTS_CONFIGURATION', TESTS_ROOT . '/configuration');
}

if (!defined('LIBRARY_PATH')) {
    define('LIBRARY_PATH', dirname(TESTS_ROOT) . '/library/umi');
}

$loader = require_once($vendorDir . '/autoload.php');
$loader->add('utest', __DIR__);
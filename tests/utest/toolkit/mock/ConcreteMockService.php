<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

/**
 * Конкретная реализация тестового сервиса
 */
class ConcreteMockService implements IMockService
{

    /**
     * @return string
     */
    public function getName()
    {
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
    }
}

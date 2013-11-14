<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\objectset;

use umi\orm\objectset\EmptyObjectSet;
use utest\orm\ORMTestCase;

/**
 * Тест пустого набора объектов
 */
class EmptyObjectSetTest extends ORMTestCase
{

    public function testEmptyObjectSet()
    {

        $objectSet = new EmptyObjectSet();

        $this->assertEmpty($objectSet->fetchAll());
        $objectSet->reset();
        $this->assertEmpty($objectSet->fetchAll());
    }

}

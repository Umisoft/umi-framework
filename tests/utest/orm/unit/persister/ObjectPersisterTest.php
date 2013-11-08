<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\db\unit\persister;

use umi\orm\object\IObject;
use umi\orm\persister\IObjectPersister;
use umi\orm\persister\ObjectPersister;
use utest\TestCase;

/**
 * Тесты ObjectPersister
 */
class ObjectPersisterTest extends TestCase
{

    /**
     * @var IObjectPersister $objectPersister
     */
    protected $objectPersister;

    protected function setUpFixtures()
    {
        $this->objectPersister = new ObjectPersister();
    }

    public function testImpossibleExecuteTransaction()
    {

        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', [], [], '', false);
        $this->objectPersister->markAsNew($object);

        $e = null;
        try {
            $this->objectPersister->executeTransaction(
                function () {
                },
                [
                    $this->getDbCluster()
                        ->getDbDriver()
                ]
            );
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что невозможно выполнить никакие транзакции, если объекты не персистентны'
        );

    }

}

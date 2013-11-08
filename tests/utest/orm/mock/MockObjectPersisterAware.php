<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\mock;

use umi\orm\persister\IObjectPersisterAware;
use umi\orm\persister\TObjectPersisterAware;
use utest\IMockAware;

class MockObjectPersisterAware implements IObjectPersisterAware, IMockAware
{

    use TObjectPersisterAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->getObjectPersister();
    }
}

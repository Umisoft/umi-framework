<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\mock;

use umi\orm\metadata\IMetadataManagerAware;
use umi\orm\metadata\TMetadataManagerAware;
use utest\IMockAware;

class MockMetadataManagerAware implements IMetadataManagerAware, IMockAware
{

    use TMetadataManagerAware;

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->getMetadataManager();
    }
}

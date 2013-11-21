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
 * {@inheritdoc}
 */
class ServicingMock implements MockServicingInterface
{

    public $dependency;
    public $service;
    public $initializerService;

    /**
     * {@inheritdoc}
     */
    public function setDependency($value)
    {
        $this->dependency = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setService($value)
    {
        $this->service = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setInitializerService($value)
    {
        $this->initializerService = $value;

        return $this;
    }
}

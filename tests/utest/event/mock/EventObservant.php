<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\event\mock;

use umi\event\IEventObservant;
use umi\event\TEventObservant;

/**
 * Class EventObservant
 */
class EventObservant implements IEventObservant
{
    use TEventObservant;

    public $name;

    public $names = [];

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->fireEvent(
            'testEventBeforeSetName',
            array(
                'name' => &$name
            )
        );
        $this->name = $name;
    }
}
 
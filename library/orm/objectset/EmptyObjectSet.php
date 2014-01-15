<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

/**
 * Пустой набор объектов.
 */
class EmptyObjectSet extends ObjectSet implements IEmptyObjectSet
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->isCompletelyLoaded = true;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        return $this;
    }
}

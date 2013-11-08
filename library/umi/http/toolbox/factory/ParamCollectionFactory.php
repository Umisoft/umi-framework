<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\toolbox\factory;

use umi\http\request\param\IParamCollectionFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика коллекции параметров.
 */
class ParamCollectionFactory implements IParamCollectionFactory, IFactory
{

    use TFactory;

    /**
     * @var array $paramCollectionClass классы коллекций параметров
     */
    public $paramCollectionClass = 'umi\http\request\param\ParamCollection';

    /**
     * {@inheritdoc}
     */
    public function createParamCollection(array &$params)
    {
        return $this->createInstance(
            $this->paramCollectionClass,
            [&$params],
            ['umi\http\request\param\IParamCollection']
        );
    }
}

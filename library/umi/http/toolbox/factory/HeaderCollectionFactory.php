<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\toolbox\factory;

use umi\http\response\header\IHeaderCollectionFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания списка заголовков HTTP ответа.
 */
class HeaderCollectionFactory implements IHeaderCollectionFactory, IFactory
{
    use TFactory;

    /**
     * @var string $headerCollectionClass класс коллекции заголовков ответа
     */
    public $headerCollectionClass = 'umi\http\response\header\HeaderCollection';

    /**
     * {@inheritdoc}
     */
    public function createHeaderCollection()
    {
        return $this->getPrototype(
                $this->headerCollectionClass,
                ['umi\http\response\header\HeaderCollection']
            )
            ->createInstance();
    }
}
 
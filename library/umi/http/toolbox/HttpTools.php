<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\toolbox;

use umi\http\IHttpAware;
use umi\http\request\param\IParamCollectionAware;
use umi\http\request\param\IParamCollectionFactory;
use umi\http\response\header\IHeaderCollectionAware;
use umi\http\response\header\IHeaderCollectionFactory;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с HTTP запросом и ответом.
 */
class HttpTools implements IHttpTools
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'http';

    use TToolbox;

    /**
     * @var string $httpFactoryClass класс фабрики для http элементов
     */
    public $httpFactoryClass = 'umi\http\toolbox\factory\HttpFactory';
    /**
     * @var string $requestParamCollectionFactoryClass класс фабрики для коллекции параметров
     */
    public $requestParamCollectionFactoryClass = 'umi\http\toolbox\factory\ParamCollectionFactory';
    /**
     * @var string $responseHeaderCollectionFactoryClass класс фабрики для коллекции заголовков
     */
    public $responseHeaderCollectionFactoryClass = 'umi\http\toolbox\factory\HeaderCollectionFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'http',
            $this->httpFactoryClass,
            ['umi\http\IHttpFactory']
        );

        $this->registerFactory(
            'paramCollection',
            $this->requestParamCollectionFactoryClass,
            ['umi\http\request\param\IParamCollectionFactory']
        );

        $this->registerFactory(
            'headerCollection',
            $this->responseHeaderCollectionFactoryClass,
            ['umi\http\response\header\IHeaderCollectionFactory']
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getHttpFactory()
    {
        return $this->getFactory('http');
    }

    /**
     * Возвращает фабрику коллекций параметров.
     * @return IParamCollectionFactory
     */
    protected function getParamCollectionFactory()
    {
        return $this->getFactory('paramCollection');
    }

    /**
     * Возвращает фабрику заголовков HTTP ответа.
     * @return IHeaderCollectionFactory
     */
    protected function getHeaderCollectionFactory()
    {
        return $this->getFactory('headerCollection');
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\http\request\IRequest':
                return $this->getHttpFactory()
                    ->getRequest();

            case 'umi\http\IHttpFactory':
                return $this->getHttpFactory();
            default:
                return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IHttpAware) {
            $object->setHttpFactory($this->getHttpFactory());
        }

        if ($object instanceof IParamCollectionAware) {
            $object->setParamCollectionFactory($this->getParamCollectionFactory());
        }

        if ($object instanceof IHeaderCollectionAware) {
            $object->setHttpHeaderCollectionFactory($this->getHeaderCollectionFactory());
        }
    }
}
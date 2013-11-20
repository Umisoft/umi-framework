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
use umi\http\IHttpFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с HTTP запросом и ответом.
 */
class HttpTools implements IToolbox
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
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'http',
            $this->httpFactoryClass,
            ['umi\http\IHttpFactory']
        );
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
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IHttpAware) {
            $object->setHttpFactory($this->getHttpFactory());
        }
    }

    /**
     * Возвращает фабрику HTTP сущностей.
     * @return IHttpFactory
     */
    protected function getHttpFactory()
    {
        return $this->getFactory('http');
    }
}
